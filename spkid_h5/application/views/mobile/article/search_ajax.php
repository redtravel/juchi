<?php  foreach($list as $article): ?>
<a class="external" href="/article/detail/<?php echo $article['id']?>">
                                        <div class="l-box1 clearfix">
                                            <div class="t-photo1"><img src="<?php echo $article['cover']?>" alt="<?php echo $article['title']?>"/></div>
                                            <div class="l-infobox2">
                                            <p class="l-item1 article-tit"><?php echo $article['title'];if(isset($article['video'])):?><span class="shipin-hu"></span><?php endif;?></p>
                                            	<p class="t-name1 article-write">作者:<?php echo $article['author']?><span><?php echo current(explode(' ', $article['post_date']))?></span></p>
                                            </div>
                                        </div>
                                    </a>
<?php endforeach;?>
