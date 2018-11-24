<style>
  .table .checkbox {
    margin-top: 0;
    margin-bottom: 0;
  }

  .table-hover > tbody > tr:hover {
    background-color: #fefcb8;
  }

  .table > tbody > tr > td {
    vertical-align: middle;
  }

  tr > th,
  tr > td:first-child,
  tr > td:last-child,
  tr > td:nth-child(3) {
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
      <button type="button" class="btn btn-primary btn-block" id="add-btn">新增</button>
    </div>

    <div class="col-sm-8 col-md-6">
      <div class="row">
        <div class="col-sm-12">
          <table class="table table-hover">
            <thead>
              <tr>
                <th class="bg-primary">順序</th>
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
  <tr>
    <td>{{:sort}}</td>
    <td>
      {{if id}}代碼：{{:id}} {{/if}} |
      {{if role}}身分：{{:role}} {{/if}}
    </td>
    <td>
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-warning edit-btn" data-sn="{{:sn}}">修改</button>
        <button type="button" class="btn btn-danger del-btn" data-sn="{{:sn}}">刪除</button>
      </div>
    </td>
    <td>
      <div class="checkbox">
        <label>
          <input type="checkbox" class="cbox" {{if enable}}checked{{/if}}  value="{{:sn}}">
        </label>
      </div>
    </td>
  </tr>
</script>

<script>
;(function($){

    const baseURL = document.URL;
    const tmpl = $.templates("#myTmpl");
    const list = $("#list");
    const msgBlock = $('#msg');

    const schoolCodeInput = $('#school-code');
    const rolesSelect = $('#roles');
    const addBtn = $('#add-btn');
    addBtn.on('click', addBtnHandler);

    // 旗標：是否處理中
    let processing = false;

    $.get(`${baseURL}?op=getAllRules`).then(rules => generateList(rules));

    function generateList(rules) {

        /* 單一條 rule ==> {sn: 2, sort: 1, rule: {id: "014568", role: ["教師", "學生"]}, enable: 1} */
        /* 攤平       ==> {sn: 2, sort: 1, id: "014568", role: ["教師", "學生"], enable: 1} */

        const html = rules.map(item => tmpl.render({sn: item.sn, sort: item.sort, enable: item.enable, ...item.rule}))
            .reduce((accu, item) => accu+=item, '');
        list.html(html);
        $('.cbox').on('click', checkboxHandler)
    }

    function checkboxHandler() {
        console.log(this.value);
    }

    function addBtnHandler() {
        if (processing) return;

        const id = schoolCodeInput.val();
        const role = rolesSelect.val();
        let rule = {};
        let data;

        if (id === '' && role.length === 0) {
            return;
        }

        if (id) {
            rule.id = id;
        }

        if (role.length > 0) {
            rule.role = role;
        }

        // console.log(rule);
        addRule(rule);
    }

    function newListItem(item) {
        console.log(item);
        const html = tmpl.render({sn: item.sn, sort: item.sort, enable: item.enable, ...item.rule});
        list.append(html);

        $('.cbox').off('click', checkboxHandler).on('click', checkboxHandler);
        processing = false;
    }

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
            error: showMsg
        });
    }

    function showMsg(e) {
        console.log(e);
        msgBlock.addClass('show');
        setTimeout(() => msgBlock.removeClass('show'), 3000);
    }
})($);

</script>
