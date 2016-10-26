/*
 * 这是一个自定义的验证类
 * 如有需要,可自己修改或补充验证函数
 * @author sean
 */

/**
 * 给string添加trim函数
 */
String.prototype.trim = function () {
  return this .replace(/^\s\s*/, '' ).replace(/\s\s*$/, '' );
}

function Validate(){
    /*
     * 验证是否为空
     */
    this.isNull=function(str){
        if(null==str||""==str.trim())
            return false;
        return true;
    }

    /*
     * 验证是否是邮箱
     */
    this.isMail=function(str){
        if(!this.isNull(str))
            return false;
        var patrn=/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$/i;
        return patrn.test(str);
    }

    /*
     * 验证是否是手机号码
     */
    this.isPhone=function(str){
        if(!this.isNull(str))
            return false;
        var patrn=/^1[358]\d{9}$/;  
        return patrn.test(str);
    }
}
