<style>
  .table .checkbox {
    margin-top: 0;
    margin-bottom: 0;
  }

  tr.selected,
  .table-hover > tbody > tr:hover {
    background-color: #fefcb8;
  }

  .table > tbody > tr > td {
    vertical-align: middle;
  }

  tr > th,
  tr > td:not(:nth-child(2)) {
    text-align: center;
  }

  label {
    width: 100%;
    height: 100%;
  }

  .checkbox label {
    padding-left: 0;
  }

  .checkbox input[type="checkbox"] {
    margin-left: 0;
    cursor: pointer;
  }

  #msg {
    display: none;
    width: 250px;
    padding: 20px;
    position: absolute;
    left: 50%;
    top: 50px;
    text-align: center;
    background-color: #faf6d1;
    border: 1px solid #b8b8b8;
    border-radius: 10px;
    transform: translateX(-50%);
  }

  #msg.show {
    display: block;
  }
</style>

<div class="container-fluid">
  <div class="page-header">
    <h2>登入規則</h2>
  </div>

  <div class="row">

    <div class="col-sm-4 col-md-3">
      <div class="form-group">
        <label for="school-code">學校/單位代碼</label>
        <input type="text" class="form-control" id="school-code" placeholder="<{$data.schoolCode}>" value="<{$data.schoolCode}>">
      </div>
      <div class="form-group">
        <label for="roles">身分</label>
        <select multiple class="form-control" id="roles">
          <option>教師</option>
          <option>學生</option>
          <option>家長</option>
          <option>志工</option>
        </select>
      </div>

      <div class="row">
        <div class="col-sm-6"><button type="button" class="btn btn-primary btn-block" id="add-edit-btn">新增</button></div>
        <div class="col-sm-6"><button type="button" class="btn btn-default btn-block" id="cancel-btn">取消</button></div>
      </div>


    </div>

    <div class="col-sm-8 col-md-6">
      <div class="row">
        <div class="col-sm-12">
          <table class="table table-hover">
            <thead>
              <tr>
                <th class="bg-primary">#</th>
                <th class="bg-primary">規則</th>
                <th class="bg-primary">管理</th>
                <th class="bg-primary">啟用</th>
              </tr>
            </thead>
            <tbody id="list">

            </tbody>
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
      {{if rule.id}}代碼：{{:rule.id}} {{/if}} |
      {{if rule.role}}身分：{{:rule.role}} {{/if}}
    </td>
    <td>
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-warning edit-btn" id="edit-{{:sn}}" data-sn="{{:sn}}">修改</button>
        <button type="button" class="btn btn-danger del-btn" id="del-{{:sn}}" data-sn="{{:sn}}">刪除</button>
      </div>
    </td>
    <td>
      <div class="checkbox">
        <label>
          <input type="checkbox" class="cbox" id="check-{{:sn}}" {{if enable}}checked{{/if}}  value="{{:sn}}">
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

    const msgBlock = $('#msg'); // 訊息區塊

    const schoolCodeInput = $('#school-code'); // 校代碼輸入
    const defaultSchoolCode = schoolCodeInput.val(); // 預設校代碼

    const rolesSelect = $('#roles'); // 身分多選選單

    const addEditBtn = $('#add-edit-btn'); // 新增/更新 按鈕
    addEditBtn.on('click', addEditBtnHandler);

    const cancelBtn = $('#cancel-btn'); // 取消按鈕
    cancelBtn.on('click', resetAll);

    let editSN = null; // 旗標：紀錄編輯的資料 sn
    let processing = false; // 旗標：是否處理中

    let allRules = []; // 存放所有規則

    // 取得所有規則
    $.get(`${baseURL}?op=getAllRules`)
      .then(rules => generateList(rules))
      .fail(err => showMsg(err, '取得所有規則時發生錯誤'))
      .done(makeListSortable); // 啟動拖拉排序

    /********* function 區 *********/

    // 產生表格清單
    function generateList(rules) {
        allRules = rules;
        /* 單一條 rule ==> {sn: 2, rule: {id: "014568", role: ["教師", "學生"]}, enable: 1} */

        const html = rules.map(item => tmpl.render(item))
            .reduce((accu, item) => accu+=item, '');
        list.html(html);

        $('.edit-btn').on('click', editBtnHandler);
        $('.del-btn').on('click', delBtnHandler);
        $('.cbox').on('click', checkboxHandler);
    }

    // 重設 ui 與 旗標狀態
    function resetAll() {
        addEditBtn.text('新增').removeClass('btn-warning');
        $(`#sn_${editSN}`).removeClass('selected');
        editSN = null;
        processing = false;
        clearAllRuleFormInput();
    }

    // 重設輸入表單
    function clearAllRuleFormInput() {
        processing = false;
        schoolCodeInput.val(defaultSchoolCode);
        rolesSelect.val('');
    }

    // 編輯按鈕 handler
    function editBtnHandler() {
        if (processing) return;
        // console.log('edit => ', $(this).data('sn'));
        editSN = $(this).data('sn');

        $(`#sn_${editSN}`).addClass('selected');

        // 找出編輯的 rule
        const editedRule = allRules.find(rule => rule.sn === editSN );
        // 更新畫面輸入元素之值
        addEditBtn.text('更新').addClass('btn-warning');
        schoolCodeInput.val(editedRule.rule.id);
        rolesSelect.val(editedRule.rule.role);
    }

    // 刪除按鈕 handler
    function delBtnHandler() {
        if (processing) return;
        // console.log('del => ',$(this).data('sn'));
        if (confirm('確定刪除？')) {
            delRule($(this).data('sn'));
        }
    }

    // 啟用 / 停用 checkbox handler
    function checkboxHandler() {
        console.log('toggle active => ', this.value);
        processing = true;
        $.get(`${baseURL}?op=toggleRuleActive&sn=${this.value}`)
         .then(() => {
             // 改 allRules
             // 改 checkbox checked
         })
         .fail(err => showMsg(err, '規則 啟用 / 停用 時發生錯誤'))
         .done(resetAll);
    }

    // 新增 / 編輯 按鈕 handler
    function addEditBtnHandler() {
        if (processing) return;

        const id = schoolCodeInput.val().trim();
        const role = rolesSelect.val();
        let rule = {};
        let data;

        if (id === '' && role.length === 0) {
            // 校代碼為空 且 未選擇身分
            return;
        }

        if (id) {
            rule.id = id;
        }

        if (role.length > 0) {
            rule.role = role;
        }

        // console.log(rule);

        // 執行 更新 或 新增
        editSN ? updateRule({sn: editSN, rule}) : addRule(rule);
    }

    // 新增表格清單項目
    function newListItem(item) {
        // console.log(item);
        allRules.push(item); // 加入新規則

        // 附加到列表畫面
        const html = $(tmpl.render(item)).addClass('selected').hide().fadeIn(1000);
        list.append(html);
        registerListItemClickHandlers(item.sn);
        setTimeout(() => html.removeClass('selected'), 1500);
        processing = false;
    }

    // 更新表格清單項目
    function updateListItem(item) {
        // console.log('after update => ', item);
        // 替換舊規則
        const idx = allRules.findIndex(rule => rule.sn === item.sn);
        allRules.splice(idx, 1, item);

        $(`#sn_${item.sn}`).fadeOut(500, function() {
            const html = $(tmpl.render(item)).addClass('selected').hide().fadeIn(1000);
            $(this).replaceWith(html);
            registerListItemClickHandlers(item.sn);
            setTimeout(() => html.removeClass('selected'), 1500);
        });
        addEditBtn.removeClass('btn-warning');
        processing = false;
    }

    // 刪除表格清單項目
    function delListItem(sn) {
        // allRules 中移除
        const idx = allRules.findIndex(rule => rule.sn === sn);
        allRules.splice(idx, 1);
        // list 中移除 dom element
        $(`#sn_${sn}`).css('background-color', '#ff7983').fadeOut(500, function() {
            $(this).remove();
        });
    }

    // 註冊表格清單指定項目之 編輯 / 刪除 / 啟用停用 handler
    function registerListItemClickHandlers(sn) {
        $(`#edit-${sn}`).on('click', editBtnHandler);
        $(`#del-${sn}`).on('click', delBtnHandler);
        $(`#check-${sn}`).on('click', checkboxHandler);
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
            complete: clearAllRuleFormInput
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
            complete: resetAll
        });
    }

    // 刪除規則 ajax request
    function delRule(sn) {
        processing = true;
        $.get(`${baseURL}?op=delRule&sn=${sn}`)
         .then(() => delListItem(sn))
         .fail(err => showMsg(err, '刪除規則時發生錯誤'))
         .done(resetAll);
    }

    // 顯示訊息區塊
    function showMsg(err, msg) {
        console.log(err);
        msgBlock.text(msg);
        msgBlock.addClass('show');
        setTimeout(() => msgBlock.removeClass('show'), 3000);
    }

    // 啟動拖拉排序
    function makeListSortable() {
        $('#list').sortable({ opacity: 0.6, cursor: 'move', update: function() {
                let order = $(this).sortable('serialize');
                $.post('save_sort.php', order, function(theResponse){
                    //$('#save_msg').html(theResponse);
                }).fail(err => showMsg(err, '儲存排序時發生錯誤'));
            }
        });
    }

})($);

</script>
