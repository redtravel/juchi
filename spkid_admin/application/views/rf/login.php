    <form name="mainForm" method="post" action="/rf/proc_login" onsubmit="return check_form()">
        <table class="form" cellpadding=0 cellspacing=0>
            <tr>
                <td class="item_title">
                    用户名:
                </td>
                <td>
                    <input type='text' name="admin_name" id="admin_name"/>
                </td>
            </tr>
            <tr>
                <td class="item_title">
                    密码:
                </td>
                <td>
                    <input type='password' name="admin_password" id="admin_password"/>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="登录">
                </td>
            </tr>
        </table>
    </form>
    <script>
        function check_form(){
		    var validator = new Validator('mainForm');
			validator.required('admin_name', '请填写用户名');
			validator.required('admin_password', '请填写密码');
			return validator.passed();
	    }
    </script>
