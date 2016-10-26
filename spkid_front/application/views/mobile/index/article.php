<?php foreach($articles as $article): ?>
                            <li>                               
                                <div class="edu-box1">
				  <div class="juli-plick">
                                    <a class="external" href="/article/detail/<?php echo $article['id']?>">
                                        <div class="l-box1 clearfix">
                                            <div class="t-photo1"><img src="<?php echo $article['cover']?>" alt="<?php echo $article['title']?>"/></div>
                                            <div class="l-infobox2">
                                            <p class="l-item1 article-tit"><?php echo $article['title'];if(isset($article['video'])):?><span class="shipin-hu"></span><?php endif;?></p>
                                            	<p class="t-name1 article-write">作者:<?php echo $article['author']?><span><?php echo current(explode(' ', $article['date']))?></span></p>
                                                <p class="l-sale1 article-new"><?php echo $article['intro']?></p>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="status-line2 clearfix">
                                        <div class="article-ico">
            					            <div class="attention"><?php echo get_page_view('article',$article['id'],false);?></div>
                                            <div class="information"><?php echo $article['total']?></div>
            					        </div>
                                        <?php if( !empty($collect_data) && deep_in_array($article['id'], $collect_data)) { ?>
                                            <div class="article-heart article-heart-red"></div>
                                        <?php }else{ ?>
                                            <div class="article-heart article-heart-gray" onclick="add_to_collect(<?php echo $article['id'];?>,2,this,'article-heart');"></div>
                                        <?php } ?>                                          
                                    </div>
				    </div>
                                 </div>                               
                            </li>
<?php endforeach;?>
