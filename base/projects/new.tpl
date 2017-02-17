         <div class="row-fluid">
            <div class="span12">
              <div class="widget">
                <div class="widget-header">
                  <div class="title">
                    <span class="fs1" aria-hidden="true" data-icon="&#xe023;"></span> Create new project
                  </div>
                </div>
                <div class="widget-body">

<?php include(BASE_PATH . "-status.tpl"); ?>
                
                  <form class="form-horizontal no-margin" method="post">
                    <div class="control-group">
                      <label class="control-label" for="name">Project title</label>
                      <div class="controls"><input class="span8" id="name" name="name" value="<?php echo getPost('name');?>" type="text" placeholder="Project title"></div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="urle">Url</label>
                      <div class="controls"><input class="span8" id="url" name="url" value="<?php echo getPost('url');?>" type="text" placeholder="http://www.yourwebsite.com/"></div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="urle">Max depth URL</label>
                      <div class="controls"><input class="span8" id="urldepth" name="options[urldepth]" value="<?php echo getPostArr('options', 'urldepth');?>" type="text" placeholder="http://www.yoursite.com/group/"></div>
                    </div>
                    <hr />
                    <h5>Project options</h5>
                    <div class="control-group">
                      <label class="control-label" for="urle">Max URLS to fetch</label>
                      <div class="controls"><input class="span2" id="urlmax" name="options[urlmax]" value="<?php echo getPostArr('options', 'urlmax');?>" type="text" placeholder="1000000"> (ex: empty is all found links)</div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="blocklinks">Block link parts</label>
                      <div class="controls"><input class="span8" id="blocklinks" name="options[blocklinks]" value="<?php echo getPostArr('options', 'blocklinks');?>" type="text" placeholder="example: /downloads/;/print/;/css/"> seperate with ; to exclude</div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="urle">Validate email addresses</label>
                      <div class="controls"><input type="radio" id="validateemail" name="options[validateemail]" value="1" <?php echo getPostArr('options', 'validateemail', '1');?> />Yes 
                                            <input type="radio" id="validateemail" name="options[validateemail]" value="0" <?php echo getPostArr('options', 'validateemail', '0');?> /> No</div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="blockemails">Block email domains</label>
                      <div class="controls"><input class="span8" id="blockemails" name="options[blockemails]" value="<?php echo getPostArr('options', 'blockemails');?>" type="text" placeholder="example: email@domain.ru; .ru; hotmail.com"> seperate with ; to exclude </div>
                    </div>
                    <hr />
                    <h5>Indentify options</h5>
                    <div class="control-group">
                      <label class="control-label" for="curluseragentstring">Browser Agent String</label>
                      <div class="controls"><input class="span8" id="curluseragentstring" name="options[curluseragentstring]" value="<?php echo getPostArr('options', 'curluseragentstring');?>" type="text" placeholder="Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1468.0 Safari/537.36"></div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="curlreferer">Referer URL</label>
                      <div class="controls"><input class="span8" id="curlreferer" name="options[curlreferer]" value="<?php echo getPostArr('options', 'curlreferer');?>" type="text" placeholder="http://www.yourreferersite.com"></div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="curlcookie">Cookie</label>
                      <div class="controls"><input class="span8" id="curlcookie" name="options[curlcookie]" value="<?php echo getPostArr('options', 'curlcookie');?>" type="text" placeholder="test=1"></div>
                    </div>
                    <hr />
                    <h5>Proxy options</h5>
                    <div class="control-group">
                      <label class="control-label" for="proxyurl">IP</label>
                      <div class="controls"><input class="span4" id="proxyip" name="options[proxyip]" value="<?php echo getPostArr('options', 'proxyip');?>" type="text" placeholder="1.2.3.4"></div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="proxyport">Port</label>
                      <div class="controls"><input class="span4" id="proxyport" name="options[proxyport]" value="<?php echo getPostArr('options', 'proxyport');?>" type="text" placeholder="12345"></div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="proxyusername">Username</label>
                      <div class="controls"><input class="span4" id="proxyusername" name="options[proxyusername]" value="<?php echo getPostArr('options', 'proxyusername');?>" type="text" placeholder="username"> (optional) </div> 
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="proxypassword">Password</label>
                      <div class="controls"><input class="span4" id="proxypassword" name="options[proxypassword]" value="<?php echo getPostArr('options', 'proxypassword');?>" type="text" placeholder=""> (optional) </div> 
                    </div>
                    <hr />
                    <h5>Results</h5>
                    <div class="control-group">
                      <label class="control-label" for="email">Email address</label>
                      <div class="controls"><input class="span4" id="email" name="options[email]" value="<?php echo getPostArr('options', 'email');?>" type="text" placeholder="your email address"></div>
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-info pull-right">Create</button>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
