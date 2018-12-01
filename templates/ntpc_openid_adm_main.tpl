<link href="<{$xoops_url}>/modules/ntpc_openid/templates/ntpc_openid.css" rel="stylesheet">
<style>
  tr > td:not(:nth-child(2)) {
    text-align: center;
  }

  th.sort-handler {
    width: 20px;
  }
</style>

<div class="container-fluid">
  <div class="page-header">
    <h2>登入規則<small>限制登入身分</small></h2>
  </div>

  <div class="row">
    <div class="col-sm-12">

      <div class="flex-container">

        <!--////////////   左側區塊   ////////////-->
        <div class="flex-container flex-column left-block">
          <div class="form-group">
            <label for="school-code">學校/單位代碼</label>
            <input type="text" class="form-control" id="school-code" placeholder="<{$data.schoolCode}>" value="<{$data.schoolCode}>">
          </div>

          <div class="form-group">
            <label for="roles">身分 <small class="text-danger text-hint">可複選</small></label>
            <select multiple class="form-control" id="roles">
              <option>教師</option>
              <option>學生</option>
              <option>家長</option>
              <option>志工</option>
            </select>
          </div>

          <div class="flex-container">
            <div class="flex-1" style="margin-right: 10px;"><button type="button" class="btn btn-primary btn-block" id="add-edit-btn">新增</button></div>
            <div class="flex-1"><button type="button" class="btn btn-default btn-block" id="cancel-btn">取消</button></div>
          </div>
        </div>

        <!--////////////   右側區塊   ////////////-->
        <div class="right-block">

          <table class="table table-hover">
            <thead>
              <tr>
                <th class="bg-primary sort-handler">#</th>
                <th class="bg-primary">規則</th>
                <th class="bg-primary tool">管理</th>
                <th class="bg-primary enable">啟用</th>
              </tr>
            </thead>
            <tbody id="list"></tbody>
          </table>

        </div>

      </div>

    </div>
  </div>

  <div id="msg">msg</div>
</div>



<script src="//cdnjs.cloudflare.com/ajax/libs/jsrender/0.9.91/jsrender.min.js"></script>
<!--
  jsrender 參考文件
  https://www.jsviews.com/#download/pages-jsr-jq
  https://www.jsviews.com/
  https://www.jsviews.com/#jsrapi
-->
<script id="myTmpl" type="text/x-jsrender">
  <tr id="sn_{{:sn}}">
    <td>
      <img src="<{$xoops_url}>/modules/ntpc_openid/images/icons/menu.png" style="cursor: move;margin:0px 4px;" alt="<{$smarty.const._TAD_SORTABLE}>" title="<{$smarty.const._TAD_SORTABLE}>">
    </td>
    <td>
      {{if rule.id}}<span class="item">代碼：{{:rule.id}}</span>{{/if}}
      {{if rule.role}}<span class="item">身分：{{:rule.role}}</span>{{/if}}
    </td>
    <td>
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-warning edit-btn" data-action="edit" data-sn="{{:sn}}">修改</button>
        <button type="button" class="btn btn-danger del-btn" data-action="del" data-sn="{{:sn}}">刪除</button>
      </div>
    </td>
    <td>
      <div class="checkbox">
        <label>
          <input type="checkbox" class="cbox" {{if enable}}checked{{/if}} data-action="toggle" data-sn="{{:sn}}">
        </label>
      </div>
    </td>
  </tr>
</script>

<script>
;(function($){

    const baseURL = document.URL; // admin/main.php 之 URL

    const tmpl = $.templates("#myTmpl");
    const list = $("#list");
    list.on('click', listClickHandler);

    const msgBlock = $('#msg'); // 訊息區塊

    const schoolCodeInput = $('#school-code'); // 校代碼輸入
    const defaultSchoolCode = schoolCodeInput.val(); // 預設校代碼
    const rolesSelect = $('#roles'); // 身分多選選單
    const addEditBtn = $('#add-edit-btn'); // 新增/更新 按鈕
    addEditBtn.on('click', addEditBtnHandler);
    const cancelBtn = $('#cancel-btn'); // 取消按鈕
    cancelBtn.on('click', resetAll);

    // flags
    let editItem = null; // 紀錄編輯的資料 sn
    let processing = false; // 是否處理中

    // data
    let allRules = []; // 存放所有規則


    // 取得所有規則
    getAllRules();

    /********* function 區 *********/

    // list's click handler
    function listClickHandler(event) {
      if (processing) return false;

      const target = $(event.target);
      const action = target.data('action');
      const sn = target.data('sn');
      // console.log(action, sn);

      switch (action) {
        case 'edit':
          editItem = allRules.find(item => item.sn === sn);
          // console.log(editItem);
          fillForm(editItem);
          target.closest('tr').addClass('selected').siblings().removeClass('selected added modified');
          break;

        case 'del':
          if (confirm('確定刪除？')) {
              // console.log('真的要刪！！');
              delRule(sn);
          }
          break;

        case 'toggle':
          toggleRuleEnable(sn);
          break;
      }
    }

    // 新增 / 更新 按鈕 click handler
    function addEditBtnHandler() {
      if (processing || ! canSubmit()) return false;

      // 群組規則
      const rule = {};

      // 校代碼
      const id = schoolCodeInput.val().trim();
      if (id) rule.id = id;

      // 身分
      const role = rolesSelect.val();
      if (role.length) rule.role = role;

      // 更新 或 新增
      editItem ? updateRule({sn: editItem.sn, rule}) : addRule(rule);
    }

    // 是否可以送出
    function canSubmit() {
      // 至少有一個欄位有值才為 true
      return !!(schoolCodeInput.val() || rolesSelect.val().length);
    }

    // 產生表格清單
    function generateList(rules) {
        allRules = rules;
        /* 單一條 rule ==> {sn: 2, rule: {id: "014568", role: ["教師", "學生"]}, enable: 1} */

        const html = rules.map(item => tmpl.render(item))
            .reduce((accu, item) => accu+=item, '');
        list.html(html);
    }

    // 重設 ui 與 旗標狀態
    function resetAll() {
      resetFlags();
      resetForm();
      resetListItemClass();
    }

    // reset flags
    function resetFlags() {
      editSN = null;
      processing = false;
    }

    // 重設列表項目之 css class
    function resetListItemClass() {
        list.children().removeClass('selected added modified');
    }

    // 重設輸入表單
    function resetForm() {
      schoolCodeInput.val(defaultSchoolCode);
      rolesSelect.val('');

      changeAddEditBtnMode('add');
    }

    // 編輯時填充各欄位
    function fillForm({rule}) {
      schoolCodeInput.val(rule.id || '');
      rolesSelect.val(rule.role || '');

      changeAddEditBtnMode('edit');
    }

    // 變更 新增 / 更新 鈕狀態
    function changeAddEditBtnMode(mode = 'add') {
      switch (mode) {
        case 'edit':
          addEditBtn.text('更新').addClass('btn-warning');
          break;
        case 'add':
        default:
          addEditBtn.text('新增').removeClass('btn-warning');
      }
    }

    // 刪除規則 ajax request
    function delRule(sn) {
      processing = true;
      $.get(`${baseURL}?op=delRule&sn=${sn}`)
       .then(() => delListItem(sn))
       .fail(err => showMsg(err, '刪除規則時發生錯誤'))
       .done(resetAll);
    }

    // 刪除表格清單項目
    function delListItem(sn) {
      // allRules 中移除
      const idx = allRules.findIndex(rule => rule.sn === sn);
      allRules.splice(idx, 1);
      // list 中移除 dom element
      list.find(`tr#sn_${sn}`).css('background-color', '#ff7983').fadeOut(500, function() {
          $(this).remove();
      });
    }

    // 顯示訊息區塊
    function showMsg(err, msg) {
      console.log(err);
      msgBlock.text(msg);
      msgBlock.addClass('show');
      setTimeout(() => msgBlock.removeClass('show'), 3000);
    }

    // 取得所有規則
    function getAllRules() {
      $.get(`${baseURL}?op=getAllRules`)
       .then(rules => generateList(rules))
       .fail(err => showMsg(err, '取得所有規則時發生錯誤'))
       .done(makeListSortable); // 啟動拖拉排序
    }

    // 新增規則 ajax request
    function addRule(data) {
      processing = true;
      $.ajax({
        type: 'POST',
        url: `${baseURL}?op=addRule`,
        async: true,
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: newRule => newListItem(newRule.result),
        error: err => showMsg(err, '新增時發生錯誤'),
        complete: () => {
          resetForm();
          resetFlags();
        }
      });
    }

    // 新增表格清單項目
    function newListItem(item) {
      // console.log(item);
      allRules.push(item); // 加入新規則

      // 附加到列表畫面
      // const html = $(tmpl.render(item)).addClass('selected').hide().fadeIn(1000);
      const html = $(tmpl.render(item)).addClass('ui-sortable-handle added');
      list.append(html);
    }

    // 啟用 / 停用
    function toggleRuleEnable(sn) {
      processing = true;
      const url = `${baseURL}?op=toggleRuleActive&sn=${sn}`;
      $.get(url)
       .then(() => {
         // 更新 allRules
         const rule = allRules.find(item => item.sn === sn);
         rule.enable = +(!rule.enable);
         // console.log(rule, allRules);
       })
       .fail(err => showMsg(err, '啟用 / 停用時發生錯誤'))
       .done(() => {
         resetAll();
       });
    }

    // 更新規則 ajax request
    function updateRule(data) {
      processing = true;
      $.ajax({
        type: 'POST',
        url: `${baseURL}?op=updateRule`,
        async: true,
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: updatedRule => updateListItem(updatedRule.result),
        error: err => showMsg(err, '更新時發生錯誤'),
        complete: () => {
          resetForm();
          resetFlags();
        }
      });
    }

    // 更新表格清單項目
    function updateListItem(item) {
      // console.log('after update => ', item);
      // 替換舊規則
      const idx = allRules.findIndex(rule => rule.sn === item.sn);
      allRules.splice(idx, 1, item);

      list.find(`tr#sn_${item.sn}`).fadeOut(500, function () {
          const updated = tmpl.render(item);
          $(this).replaceWith($(updated).addClass('modified'));
      });
    }

    // 啟動拖拉排序
    function makeListSortable() {
      $('#list').sortable({ opacity: 0.6, cursor: 'move', update: function() {
          $(this).children('tr').removeClass('added modified');

          let order = $(this).sortable('serialize');
          $.post('save_sort.php', order, function(theResponse){
              // do something
          }).fail(err => showMsg(err, '儲存排序時發生錯誤'));
        }
      });
    }

})($);

</script>
