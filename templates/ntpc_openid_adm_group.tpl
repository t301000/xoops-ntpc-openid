<link href="<{$xoops_url}>/modules/ntpc_openid/templates/ntpc_openid.css" rel="stylesheet">
<style>
  tr > td:first-child {
    text-align: left;
  }

  tr > td:not(:first-child) {
    text-align: center;
  }
</style>

<div class="container-fluid">
  <div class="page-header">
    <h2>群組規則</h2>
  </div>

  <div class="row">
    <div class="col-sm-12 col-md-4 col-lg-3">

      <div class="row">

        <div class="col-sm-6">

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

        </div>

        <div class="col-sm-6">

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

        </div>



      </div>

      <div class="row">
        <div class="col-sm-6"><button type="button" class="btn btn-primary btn-block" id="add-edit-btn">新增</button></div>
        <div class="col-sm-6"><button type="button" class="btn btn-default btn-block" id="cancel-btn">取消</button></div>
      </div>





    </div>
  </div>

  <div class="row">

    <div class="col-sm-12 col-md-8">

      <table class="table table-hover">
        <thead>
          <tr>
            <th class="bg-primary">規則</th>
            <th class="bg-primary">群組</th>
            <th class="bg-primary">管理</th>
            <th class="bg-primary">啟用</th>
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

</script>

<script>
;(function($){

})($);
</script>

