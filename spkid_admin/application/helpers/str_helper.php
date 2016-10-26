<?
/**
 * 计算字符串长度
 * 汉字占两个字符
 */
function my_strlen($str)
{
    if(empty($str))return 0;
    return (strlen($str) + mb_strlen($str,'UTF8')) / 2;
}
?>
