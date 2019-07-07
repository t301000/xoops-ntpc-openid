<{if count($block) > 0}>
    <form action="/modules/ntpc_openid/index.php" method="post">
        <div class="form-group">
            <select class="form-control" name="to_uid" id="proxy_user_select">
                <option value="">--- 選擇 ---</option>
                <{foreach from=$block item=user}>
                <option value="<{$user.uid}>"><{$user.name}></option>
                <{/foreach}>
            </select>
        </div>
        <div class="text-center">
            <button class="btn btn-primary mb-2" type="submit" id="proxy_start" disabled>開始代理</button>
            <{if $smarty.session.proxyFromUid}>
                <br>
                <a class="btn btn-info" href="/modules/ntpc_openid/index.php?op=proxy_user_end" role="button">結束代理</a>
            <{/if}>
        </div>
        <input type="hidden" name="op" value="proxy_user_start">
        <input type="hidden" name="fromUrl">
    </form>

    <script>
        ;(function(location){
            const uid = document.querySelector('#proxy_user_select');
            const startBtn = document.querySelector('#proxy_start');
            const fromUrl = document.querySelector('input[name=fromUrl]');
            fromUrl.value = location.href;

            // 依下拉選單之值決定按鈕是否禁用
            uid.addEventListener('change', function() {
                startBtn.disabled = !this.value;
            })
        })(location);
    </script>
<{/if}>
