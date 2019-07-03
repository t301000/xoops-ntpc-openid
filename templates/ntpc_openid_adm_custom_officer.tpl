<link href="<{$xoops_url}>/modules/ntpc_openid/templates/ntpc_openid.css" rel="stylesheet">
<style>
  #list .checkbox {
    width: 150px;
    user-select: none;
  }
</style>

<div class="container-fluid">
  <div class="page-header">
    <h2>自定義行政帳號<small>建立非標準行政帳號</small></h2>
  </div>

  <div class="row">
    <div class="col-sm-12">

      <div class="flex-container flex-wrap" id="list"></div>

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
  <div id="sn_{{:sn}}" class="officer">
    <div class="checkbox {{if !enable}}text-muted{{/if}}">
      <label data-sn={{:sn}}>
         <input type="checkbox" data-sn={{:sn}} {{if enable}}checked{{/if}}> {{:name}} ({{:openid}})
      </label>
    </div>
  </div>
</script>

<script>
;(function($){
  const baseURL = document.URL; // admin/officer.php 之 URL
  const tmpl = $('#myTmpl'); // template
  const list = $('#list'); // item list
  list.on('click', 'input[type=checkbox]', listClickHandler); // 事件委派只接受內部 checkbox 來的
  const msgBlock = $('#msg'); // 訊息區塊

  // state flags
  let processing = false; // 處理中

  // data
  let officers = []; // 所有公務帳號

  // 取得所有公務帳號
  getAllOfficers();


  /********* function 區 *********/

  // 清單列表之 click handler
  function listClickHandler(event) {
    if (processing) return false;
    const target = $(event.target);
    const sn = target.data('sn');
    console.log(target, sn);

    toggleOfficer(sn, target);

  }

  // 取得所有公務帳號
  function getAllOfficers() {
    processing = true;
    const url = `${baseURL}?op=getOfficerList`;
    $.get(url)
      .then(list => {
          officers = list;
          generateList();
      })
      .fail(err => showMsg(err, '取得所有行政帳號時發生錯誤'))
      .done(() => resetAll());
  }

  // 啟用 / 停用
  function toggleOfficer(sn, el) {
      processing = true;
      const url = `${baseURL}?op=toggle&sn=${sn}`;
      $.get(url)
       .then(() => {
          el.closest('div.checkbox').toggleClass('text-muted'); // toggle css class
       })
       .fail(err => showMsg(err, '啟用 / 停用行政帳號時發生錯誤'))
       .done(() => resetAll());
  }

  // 產生清單列表
  function generateList() {
      console.log(officers);
      officers.forEach(officer => {
         console.log(officer);
         const item = tmpl.render(officer);
         list.append(item);
      });
  }

  // 重設所有旗標與狀態
  function resetAll() {
      resetFlags();
  }

  // 重設所有旗標
  function resetFlags() {
      processing = false;
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

