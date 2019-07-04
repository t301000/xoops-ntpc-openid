<link href="<{$xoops_url}>/modules/ntpc_openid/templates/ntpc_openid.css" rel="stylesheet">
<style>
  #list .checkbox {
    /*width: 150px;*/
    user-select: none;
  }

  .officer {
    width: 300px;
  }
</style>

<div class="container-fluid mb-5">
  <div class="page-header">
    <h2>自定義行政帳號<small>建立非標準行政帳號</small></h2>
  </div>

  <div class="row align-items-stretch">
    <div class="col-sm-12 col-md-3 col-xl-2">
      <div class="form-group">
        <input type="text" class="form-control" id="name" placeholder="自定義行政帳號名稱" required>
      </div>
    </div>

    <div class="col-sm-12 col-md-3 col-xl-2">
      <div class="form-group">
        <input type="text" class="form-control" id="openid" placeholder="OpenID 帳號" required>
      </div>
    </div>

    <div class="col-sm-12 col-md-auto d-flex align-items-stretch">
      <div class="form-group align-self-end mr-3">
        <div class="checkbox">
          <label for="enable">
            <input type="checkbox" id="enable" checked> 啟用
          </label>
        </div>
      </div>

      <div>
        <button class="btn btn-primary" type="button" id="create">新增</button>
        <button class="btn btn-primary" type="button" id="update">更新</button>
        <button class="btn btn-danger" type="button" id="delete">刪除</button>
        <button class="btn btn-default" type="button" id="reset">取消</button>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-sm-12">

      <div class="flex-container flex-wrap" id="list"></div>
      <div id="empty-msg">---- 沒有資料喔 ----</div>

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
  <div id="sn_{{:sn}}" class="officer mb-3 flex-basis">
    <div class="checkbox {{if !enable}}text-muted{{/if}} pr-4 d-flex align-items-center">
      <label data-sn={{:sn}} class="mr-2">
         <input type="checkbox" data-sn={{:sn}} {{if enable}}checked{{/if}}> {{:name}} ({{:openid}})
      </label>
      <button class="btn btn-info mr-2" type="button" data-sn={{:sn}}>修改</button>
    </div>
  </div>
</script>

<script>
;(function($){
  const baseURL = document.URL; // admin/officer.php 之 URL
  const tmpl = $('#myTmpl'); // template
  const list = $('#list'); // item list
  list.on('click', 'input[type=checkbox]', listClickHandler); // 事件委派只接受內部 checkbox 來的
  list.on('click', 'button', listBtnClickHandler); // 事件委派只接受內部 buttton 來的
  const msgBlock = $('#msg'); // 訊息區塊
  const emptyMsg = $('#empty-msg');
  emptyMsg.hide();

  const inputName = $('#name'); // 自定義行政帳號輸入
  const inputOpenID = $('#openid'); // openid 帳號輸入
  const checkboxEnable = $('#enable'); // 啟用 checkbox
  const btnCreate = $('#create');
  const btnUpdate = $('#update');
  const btnDelete = $('#delete');
  const btnReset = $('#reset');
  btnCreate.click(ajaxCreate);
  btnUpdate.click(ajaxUpdate);
  btnDelete.click(ajaxDelete);
  btnReset.click(resetForm);

  btnUpdate.hide();
  btnDelete.hide();

  // state flags
  let processing = false; // 處理中
  let formMode = 'normal'; // normal 一般模式，可新增 edit 編輯模式，用於更新、刪除
  let formCurrentSN = null; // normal 模式下為 null，edit 模式下為當下的資料 sn

  // data
  let officers = []; // 所有公務帳號

  // 取得所有公務帳號
  getAllOfficers();


  /********* function 區 *********/

  // 新增至資料庫
  function ajaxCreate(e) {
    if (processing) return false;

    const name = inputName.val().trim();
    const openid = inputOpenID.val().trim();
    const enable = +checkboxEnable.prop('checked');

    if (name === '' || openid === '' ) return false;

    // console.log({name, openid, enable});

    processing = true;
    const url = `${baseURL}?op=createOfficer`;
    $.post(url, {name, openid, enable})
            .then(({sn}) => {
              // console.log('success', sn);
              let officer = {sn, name, openid, enable};
              officers.push(officer);
              const item = tmpl.render(officer);
              list.append(item);
              emptyMsg.hide();
              resetForm();
            })
            .fail(err => showMsg(err, `新增自定義行政帳號 ${name} 時發生錯誤`))
            .always(() => {
              resetFlags();
            });
  }

  // 更新至資料庫
  function ajaxUpdate(e) {
    if (processing) return false;

    const name = inputName.val().trim();
    const openid = inputOpenID.val().trim();
    const enable = +checkboxEnable.prop('checked');

    if (name === '' || openid === '' ) return false;

    // console.log({name, openid, enable});

    processing = true;
    const url = `${baseURL}?op=updateOfficer`;
    let newOfficer = {sn: +formCurrentSN, name, openid, enable};
    $.post(url, newOfficer)
     .then(() => {
       officers.splice(officers.findIndex(item => item.sn === newOfficer.sn), 1, newOfficer);
       const item = tmpl.render(newOfficer);
       list.find(`div#sn_${formCurrentSN}`).replaceWith(item);
     })
     .fail(err => showMsg(err, `新增自定義行政帳號 ${name} 時發生錯誤`))
     .always(() => {
       resetFlags();
     });
  }

  // 自資料庫刪除
  function ajaxDelete(e) {
    if (processing) return false;

    // console.log('before delete', officers);
    if (formCurrentSN && confirm(`確定要刪除?`)) {
      processing = true;
      const url = `${baseURL}?op=deleteOfficer`;
      $.post(url, {sn: formCurrentSN})
              .then(() => {
                officers.splice(officers.findIndex(item => item.sn === formCurrentSN), 1);
                // console.log('after delete', officers);
                list.find(`div#sn_${formCurrentSN}`).remove();
              })
              .fail(err => showMsg(err, `刪除自定義行政帳號時發生錯誤`))
              .always(() => {
                resetFlags();
                resetForm();
                officers.length > 0 ? emptyMsg.hide() : emptyMsg.show();
              });

    }
  }

  // 清單列表之 checkbox click handler
  function listClickHandler(event) {
    if (processing) return false;
    const target = $(event.target);
    const sn = target.data('sn');
    // console.log(target, sn);

    toggleOfficer(sn, target);

  }

  // 清單列表之 button click handler
  function listBtnClickHandler(event) {
    if (processing) return false;
    const target = $(event.target);
    const sn = target.data('sn');
    // console.log(`${action} => ${sn}`);

    fillForm(sn);
  }

  // 將編輯之資料填入表單
  function fillForm(sn) {
    const data = getOfficerBySN(sn);
    // console.log(data);

    if (data === undefined) return false;

    formMode = 'edit';
    formCurrentSN = sn;
    inputName.val(data.name);
    inputOpenID.val(data.openid);
    checkboxEnable.prop('checked', !!data.enable);
    btnCreate.hide();
    btnUpdate.show();
    btnDelete.show();
  }

  // 以 sn 由陣列取得資料
  function getOfficerBySN(sn) {
    return officers.find(item => item.sn === sn);
  }

  // 取得所有公務帳號
  function getAllOfficers() {
    processing = true;
    const url = `${baseURL}?op=getOfficerList`;
    $.get(url)
      .then(list => {
          officers = list;
          officers.length > 0 ? generateList() : emptyMsg.show();
      })
      .fail(err => showMsg(err, '取得所有行政帳號時發生錯誤'))
      .always(() => resetFlags());
  }

  // 啟用 / 停用
  function toggleOfficer(sn, el) {
      processing = true;
      const url = `${baseURL}?op=toggle&sn=${sn}`;
      $.get(url)
       .then(() => {
          el.closest('div.checkbox').toggleClass('text-muted'); // toggle css class
       })
       .fail(err => showMsg(err, '啟用 / 停用自定義行政帳號時發生錯誤'))
       .always(() => resetAfterToggle(sn));
  }

  // 產生清單列表
  function generateList() {
      // console.log(officers);
      officers.forEach(officer => {
         // console.log(officer);
         const item = tmpl.render(officer);
         list.append(item);
      });
  }

  // toggle 啟用 / 停用 之後重設
  function resetAfterToggle(sn) {
      resetFlags();

      if (sn && sn === formCurrentSN) {
        resetForm();
      }
  }

  // 重設所有旗標
  function resetFlags() {
      processing = false;
  }

  // 重設表單與相關旗標
  function resetForm() {
    formMode = 'normal';
    formCurrentSN = null;
    inputName.val('');
    inputOpenID.val('');
    checkboxEnable.prop('checked', true);
    btnCreate.show();
    btnUpdate.hide();
    btnDelete.hide();
  }

  // 顯示訊息區塊
  function showMsg(err, msg) {
    console.log(err);
    msgBlock.text(msg);
    msgBlock.addClass('show');
    // processing = false;
    setTimeout(() => msgBlock.removeClass('show'), 3000);
  }

})($);
</script>

