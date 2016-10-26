<?php print $header; ?>

<script type="text/javascript">
    //<![CDATA[
    window.onload = function() {
        // 更改页面title
        document.title = '<?php print $subject->subject_title; ?>';
    };
    //]]>
</script>

<?php foreach ($template_content_list as $content): ?>
    <?=$content; ?>
<?php endforeach; ?>

<?php print $footer; ?>

