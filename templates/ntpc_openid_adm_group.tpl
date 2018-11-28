<link href="<{$xoops_url}>/modules/ntpc_openid/templates/ntpc_openid.css" rel="stylesheet">
<style>
  tr > td:not(:first-child) {
    text-align: center;
  }
</style>

<div class="container-fluid">
  <div class="page-header">
    <h2>群組規則</h2>
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
                <th class="bg-primary" style="width: 100px;">群組</th>
                <th class="bg-primary tool">管理</th>
                <th class="bg-primary enable">啟用</th>
              </tr>
            </thead>
            <tbody id="list">
              <tr>
                <td>校代碼：014569 | 身分：教師</td>
                <td>教師</td>
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
              <tr>
                <td>校代碼：014569 | 身分：教師</td>
                <td>教務處教務處教師</td>
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
              <tr>
                <td>校代碼：014569 | 身分：教師</td>
                <td>教師</td>
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
      {{if rule.id}}代碼：{{:rule.id}} {{/if}} |
      {{if rule.openid}}帳號：{{:rule.openid}} {{/if}} |
      {{if rule.role}}身分：{{:rule.role}} {{/if}} |
      {{if rule.title}}職務：{{:rule.title}} {{/if}} |
      {{if rule.group}}職稱：{{:rule.group}} {{/if}}
    </td>
    <td>{{:gid}}</td>
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
  const baseURL = document.URL; // admin/group.php 之 URL

  const tmpl = $.templates("#myTmpl");
  const list = $("#list");

  const msgBlock = $('#msg'); // 訊息區塊

  const schoolCodeInput = $('#school-code'); // 校代碼輸入
  const defaultSchoolCode = schoolCodeInput.val(); // 預設校代碼

  const openidInput = $('#openid'); // 帳號輸入
  const rolesSelect = $('#roles'); // 身分多選選單
  const titlesSelect = $('#titles'); // 職務多選選單
  const groupsSelect = $('#groups'); // 職稱多選選單

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
   .done(null);

  /********* function 區 *********/




})($);
</script>

