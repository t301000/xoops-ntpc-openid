<link href="<{$xoops_url}>/modules/ntpc_openid/templates/ntpc_openid.css" rel="stylesheet">
<style>
  tr > td:not(:first-child) {
    text-align: center;
  }
</style>

<div class="container-fluid">
  <div class="page-header">
    <h2>群組規則<small>登入時自動設定群組</small></h2>
  </div>

  <div class="row">
    <div class="col-sm-12">

      <div class="flex-container">

        <!--////////////   左側區塊   ////////////-->
        <div class="flex-container flex-column left-block">
          <div class="form-group">
            <label for="gid">群組</label>
            <select class="form-control" id="gid" size="6"></select>
          </div>

          <div class="form-group">
            <label for="school-code">學校/單位代碼</label>
            <input type="text" class="form-control" id="school-code" placeholder="<{$data.schoolCode}>" value="<{$data.schoolCode}>">
          </div>

          <div class="form-group">
            <label for="openid">帳號 <small class="text-danger text-hint">可多個</small></label>
            <input type="text" class="form-control" id="openid" placeholder="user1,user2,user3" value="">
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

          <div class="form-group">
            <label for="titles">職務 <small class="text-danger text-hint">可複選</small></label>
            <select multiple class="form-control" id="titles" size="6">
              <option>校長</option>
              <option>主任</option>
              <option>教師兼主任</option>
              <option>組長</option>
              <option>教師兼組長</option>
              <option>導師</option>
              <option>專任教師</option>
              <option>實習老師</option>
              <option>試用老師</option>
              <option>代理或代課教師</option>
              <option>兼任教師</option>
              <option>職員</option>
              <option>護士</option>
              <option>警衛</option>
              <option>工友</option>
              <option>跨校人員</option>
              <option>社團教師</option>
              <option>其他</option>
            </select>
          </div>

          <div class="form-group">
            <label for="groups">職稱 <small class="text-danger text-hint">可複選</small></label>
            <select multiple class="form-control" id="groups" size="6">
              <option>校長</option>
              <option>幼兒園園長</option>
              <option>教務主任</option>
              <option>學務主任</option>
              <option>總務主任</option>
              <option>輔導主任</option>
              <option>校務主任</option>
              <option>人事主任</option>
              <option>會計主任</option>
              <option>圖書館主任</option>
              <option>幼兒園主任</option>
              <option>教學組長</option>
              <option>註冊組長</option>
              <option>資訊組長</option>
              <option>設備組長</option>
              <option>試務組長</option>
              <option>實研組長</option>
              <option>音樂組長</option>
              <option>訓育組長</option>
              <option>生活教育組長</option>
              <option>體育組長</option>
              <option>衛生組長</option>
              <option>文書組長</option>
              <option>出納組長</option>
              <option>事務組長</option>
              <option>輔導組長</option>
              <option>資料組長</option>
              <option>特教組長</option>
              <option>教保組長</option>
              <option>幼兒園組長</option>
              <option>人事助理員</option>
              <option>會計佐理員</option>
              <option>心理師</option>
              <option>社工師</option>
              <option>校護</option>
              <option>幹事</option>
              <option>教官</option>
              <option>理事長</option>
              <option>理事</option>
              <option>監事</option>
              <option>幼兒園職員</option>
              <option>幼兒園教師</option>
              <option>教保服務人員</option>
              <option>導師</option>
              <option>科任教師</option>
              <option>專任輔導教師</option>
              <option>其他</option>
              <option id="custom-opt" disabled>-- 以下為自定義職稱 --</option>
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
                <th class="bg-primary">規則</th>
                <th class="bg-primary" style="width: 120px;">群組</th>
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
      {{if rule.id}}<span class="item">代碼：{{:rule.id}}</span>{{/if}}
      {{if rule.openid}}<span class="item">帳號：{{:rule.openid}}</span>{{/if}}
      {{if rule.role}}<span class="item">身分：{{:rule.role}}</span>{{/if}}
      {{if rule.title}}<span class="item">職務：{{:rule.title}}</span>{{/if}}
      {{if rule.groups}}<span class="item">職稱：{{:rule.groups}}</span>{{/if}}
    </td>
    <td>{{:gname}}</td>
    <td>
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-warning edit-btn" data-action="edit" data-sn="{{:sn}}">修改</button>
        <button type="button" class="btn btn-danger del-btn" data-action="del" data-sn="{{:sn}}">刪除</button>
      </div>
    </td>
    <td>
      <div class="checkbox">
        <label>
          <input type="checkbox" class="cbox" {{if enable}}checked{{/if}} data-action="toggle" data-sn="{{:sn}}" value="{{:sn}}">
        </label>
      </div>
    </td>
  </tr>
</script>

<script>
;(function($){
  const baseURL = document.URL; // admin/group.php 之 URL

  const tmpl = $.templates("#myTmpl");
  const list = $("#list");
  list.on('click', listClickHandler);

  const msgBlock = $('#msg'); // 訊息區塊

  const gidSelect = $('#gid'); // 群組選擇
  const schoolCodeInput = $('#school-code'); // 校代碼輸入
  const defaultSchoolCode = schoolCodeInput.val(); // 預設校代碼
  const openidInput = $('#openid'); // 帳號輸入
  const rolesSelect = $('#roles'); // 身分多選選單
  const titlesSelect = $('#titles'); // 職務多選選單
  const groupsSelect = $('#groups'); // 職稱多選選單
  const customOptDivider = $('#custom-opt'); // 職稱多選選單中自定義職稱選項之容器
  customOptDivider.hide();

  const addEditBtn = $('#add-edit-btn'); // 新增/更新 按鈕
  addEditBtn.on('click', addEditBtnHandler);

  const cancelBtn = $('#cancel-btn'); // 取消按鈕
  cancelBtn.on('click', resetAll);

  let editItem = null; // 旗標：紀錄編輯的物件
  let processing = false; // 旗標：是否處理中

  let allRules = []; // 存放所有規則
  let allGroups = []; // 存放所有群組
  const customOfficers = <{$data.customOfficers}>; // 此處利用 smarty 語法由 php 直接給值
  // console.log(customOfficers);
  appendGroupsOption(customOfficers);
  // 取得所有規則與群組
  getAllRulesAndGroups();

  /********* function 區 *********/

  // 附加自定義職稱至選單
  function appendGroupsOption(data) {
    if (data.length > 0) {
      data.forEach(item => groupsSelect.append(`<option>${item}</option>`));
      customOptDivider.show();
    }
  }

  function resetAll() {
    resetForm();
    resetFlagData();
    resetListItemClass();
  }

  // 重設各旗標變數
  function resetFlagData() {
    editItem = null;
    processing = false;
  }

  // 重設列表項目之 css class
  function resetListItemClass() {
    list.children().removeClass('selected added modified');
  }

  // 新增 / 更新 按鈕 click handler
  function addEditBtnHandler() {
    // console.log(gidSelect.val(), openidInput.val(), schoolCodeInput.val(), rolesSelect.val(), titlesSelect.val(), groupsSelect.val());
    // console.log(canSubmit());
    if (processing || ! canSubmit()) return false;

    // 群組 id
    const gid = +gidSelect.val();
    // 群組規則
    const rule = {};

    // 校代碼
    const id = schoolCodeInput.val().trim();
    if (id) rule.id = id;

    // openid 帳號
    const openid = openidInput.val().replace(/\s/g, '');
    if (openid) rule.openid = openid.split(',');

    // 身分
    const role = rolesSelect.val();
    if (role.length) rule.role = role;

    // 職務
    const title = titlesSelect.val();
    if (title.length) rule.title = title;

    //  職稱
    const groups = groupsSelect.val();
    if (groups.length) rule.groups = groups;

    const data = {gid, rule};
    // console.log(JSON.stringify(data));

    // 更新 或 新增
    editItem ? updateRule(data) : addRule(data);

  }

  // 是否可以送出
  function canSubmit() {
    // 有選擇群組而且至少有一個欄位有值才為 true
    return !!gidSelect.val() && !!(openidInput.val() || schoolCodeInput.val() || rolesSelect.val().length || titlesSelect.val().length || groupsSelect.val().length);
  }

  // 清單 click handler，統一處理 編輯 / 刪除 / 啟用切換 之 click event
  function listClickHandler(event) {
    // console.log(event.target);
    if (processing) return false;

    const target = $(event.target);
    const action = target.data('action');
    const sn = target.data('sn');
    // console.log(action, sn);

    switch (action) {
      case 'edit': // 按下編輯
        editItem = allRules.find(item => item.sn === sn);
        // console.log(editItem);
        fillForm(editItem);
        target.closest('tr').addClass('selected').siblings().removeClass('selected added modified');
        break;

      case 'del': // 按下刪除
        if (confirm('確定刪除？')) {
          // console.log('真的要刪！！');
          delRule(sn);
        }
        break;

      case 'toggle': // 切換啟用 / 停用
        toggleRuleEnable(sn);
        break;
    }
  }

  // 產生規則列表項目 與 群組選單選項
  function generateListAndGroups({rules, xoopsGroups}) {
    generateGroupSelectOptions(xoopsGroups);
    generateList(rules);
  }

  // 產生規則列表項目
  function generateList(rules) {
    // console.log(rules)
    // rules = [{sn: 1, rule: {id: '014569', role: ['教師']}, gid: 2, enable: 1}];

    // 還沒有規則
    if (rules.length === 0) {
      list.append('<tr><td  colspan="4" class="text-center">-- 尚未設定規則 --</td></tr>');
      return false;
    }

    // 附加群組名稱
    allRules = rules.map(rule => {
      rule.gname = getXoopsGroupName(rule.gid);
      return rule;
    });
    // console.log('allRules', allRules);

    allRules.forEach(item =>{
      const html = tmpl.render(item);
      list.append(html);
    });

  }

  // 以 群組 id 取得 群組名稱
  function getXoopsGroupName(gid) {
    return allGroups.find(item => item.gid === gid).name;
  }

  // 產生群組選單選項
  function generateGroupSelectOptions(groups) {
    allGroups = groups;
    // console.log('allGroups', allGroups);

    allGroups.forEach(item => {
      let option = `<option value="${item.gid}">${item.name}</option>`;
      gidSelect.append(option);
    });
  }

  // 編輯時填充各欄位
  function fillForm({gid, rule, enable}) {
    gidSelect.val(gid || '');
    schoolCodeInput.val(rule.id || '');
    openidInput.val(rule.openid && (rule.openid.toString() || ''));
    rolesSelect.val(rule.role || '');
    titlesSelect.val(rule.title || '');
    groupsSelect.val(rule.groups || '');

    changeAddEditBtnMode('edit');
  }

    // 重設各欄位
    function resetForm() {
      gidSelect.val('');
      schoolCodeInput.val(defaultSchoolCode);
      openidInput.val('');
      rolesSelect.val('');
      titlesSelect.val('');
      groupsSelect.val('');

      changeAddEditBtnMode('add');
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

  // 顯示訊息區塊
  function showMsg(err, msg) {
    console.log(err);
    msgBlock.text(msg);
    msgBlock.addClass('show');
    // processing = false;
    setTimeout(() => msgBlock.removeClass('show'), 3000);
  }

  // 取得所有規則與群組
  function getAllRulesAndGroups() {
    $.get(`${baseURL}?op=getAllRulesAndGroups`)
     .then(data => generateListAndGroups(data))
     .fail(err => showMsg(err, '取得所有規則時發生錯誤'))
     .done(null);
  }

  // 新增
  function addRule(data) {
    processing = true;
    const url = `${baseURL}?op=addRule`;
    $.ajax({
      type: 'POST',
      url: url,
      async: true,
      data: JSON.stringify(data),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: ({sn}) => {
        // 產生新規則資料
        const newRule = {sn, ...data};
        // 補上 群組名稱 與 啟動 欄位
        newRule.gname = getXoopsGroupName(data.gid);
        newRule.enable = 1;
        allRules.push(newRule); // 加入新規則至資料
        newListItem(newRule); // 加入新列表項目至 dom
      },
      error: err => showMsg(err, '新增時發生錯誤'),
      complete: () => {
        resetForm();
        resetFlagData();
      }
    });
  }

  // 新增列表項目至 dom
  function newListItem(data) {
    // console.log(data);
    const html = tmpl.render(data);
    if (allRules.length === 1) {
      list.html('');
    }
    list.append($(html).addClass('added'));
  }

  // 更新
  function updateRule(data) {
    processing = true;
    const sn = editItem.sn;
    // console.log('update => ', sn, data);

    const url = `${baseURL}?op=updateRule&sn=${sn}`;
    $.ajax({
      type: 'POST',
      url: url,
      async: true,
      data: JSON.stringify(data),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: () => {
        // 取代舊規則
        const updated = {sn, ...data, gname: getXoopsGroupName(data.gid), enable: editItem.enable};
        allRules.splice(allRules.findIndex(item => item.sn === sn), 1, updated);
        // console.log(allRules);
        updateListItem(updated); // 加入新列表項目至 dom
      },
      error: err => showMsg(err, '更新時發生錯誤'),
      complete: () => {
        resetForm();
        resetFlagData();
      }
    });
  }

  // 更新列表項目 in dom
  function updateListItem(data) {
    list.find(`tr#sn_${data.sn}`).fadeOut(500, function () {
        const updated = tmpl.render(data);
        $(this).replaceWith($(updated).addClass('modified'));
    });
  }

  // 刪除
  function delRule(sn) {
    // console.log('sn to be del => ', sn);
    processing = true;
    const url = `${baseURL}?op=delRule&sn=${sn}`;
    $.get(url)
     .then(() => {
       // allRules 中移除
       const idx = allRules.findIndex(item => item.sn === sn);
       allRules.splice(idx, 1);
       // list 中移除 dom element
       delListItem(sn);
     })
     .fail(err => showMsg(err, '刪除時發生錯誤'))
     .done(() => {
         resetAll();
     });
  }

  // 刪除列表項目 in dom
  function delListItem(sn) {
      list.find(`tr#sn_${sn}`).css('background-color', '#ff7983').fadeOut(500, function() {
          $(this).remove();
      });
  }

  // 啟用 / 停用
  function toggleRuleEnable(sn) {
    processing = true;
    const url = `${baseURL}?op=toggleRule&sn=${sn}`;
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

})($);
</script>

