<div class="container col-md-offset-3 col-md-6">
<h1>{title}</h1>
<h2>config dir: config/{environment}</h1>
<br>
<br>
<br>
<br>

<form class="" name="setup_form" id="setup_form" action="setup/step1" method="post">
    <!-- Tabs -->
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a href="#main" data-toggle="tab">Main</a></li>
        <li role="presentation"><a href="#database" data-toggle="tab">Database</a></li>
        <li role="presentation"><a href="#messages" data-toggle="tab">Messages</a></li>
        <li role="presentation"><a href="#users" data-toggle="tab">Users</a></li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Main -->    
        <div id="main" class="tab-pane fade in active">
                    <fieldset>
                        <!-- Text input-->
                        <div class="form-group">
                            <label class=" control-label" for="base_url">Site Url</label>
                            <div class="">
                                <input id="base_url" name="config[base_url]" type="text" value="{siteurl}" class="form-control input-md" required="">
                                <span class="help-block">Enter the complete URL where the app is being hosted</span>
                            </div>
                        </div>

                        <!-- Select input-->
                        <div class="form-group">
                            <label class=" control-label" for="mongodbhost">Language</label>
                            <div class="">
                                <select id="language" name="config[language]">
                                    <option value="english">English</option>
                                    <option value="spanish">Spanish</option>
                                </select>
                                <span class="help-block">Select the main language for the system</span>
                            </div>
                        </div>

                    </fieldset>
        </div>
            
        <!-- Database -->    
        <div id="database" class="tab-pane fade in">
                                <fieldset>
                        <!-- Text input-->
                        <div class="form-group">
                            <label class=" control-label" for="mongodbhost">MongoDB Host</label>
                            <div class="">
                                <input id="mongodbhost" name="cimongo[host]" type="text" value="mongo" class="form-control input-md" required="">
                                <span class="help-block">The hostname wich will host your mongo database</span>
                            </div>
                        </div>
                        <!-- Text input-->
                        <div class="form-group">
                            <label class=" control-label" for="mongodbhost">MongoDB Database Name</label>
                            <div class="">
                                <input id="mongodbhost" name="cimongo[db]" type="text" value="dna3" class="form-control input-md" required="">
                                <span class="help-block">Database name (not need to be created)</span>
                            </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">
                            <label class=" control-label" for="mongodbport">MongoDB port</label>
                            <div class="">
                                <input id="mongodbport" name="cimongo[port]" type="text" value="27017" class="form-control input-md">
                                <span class="help-block">usually is the port 27017</span>
                            </div>
                        </div>
                        <!-- Text input-->
                        <div class="form-group">
                            <label class=" control-label" for="mongodbuser">MongoDB user</label>
                            <div class="">
                                <input id="mongodbuser" name="cimongo[user]" type="text" value="dna2bpmuser" placeholder="user" class="form-control input-md">
                                <span class="help-block">Leave it blank if no user</span>
                            </div>
                        </div>

                        <!-- Password input-->
                        <div class="form-group">
                            <label class=" control-label" for="mongodbpassword">MongoDB Password</label>
                            <div class="">
                                <input id="mongodbpassword" name="cimongo[pass]" type="password" value="kukekakuke50" placeholder="" class="form-control input-md">
                                <span class="help-block">change to same value as in docker-compose if needed</span>
                            </div>
                        </div>

                    </fieldset>
        
        </div>
        
        <!-- Messages -->    
        <div id="messages" class="tab-pane fade in">
                                <fieldset>
                        <!-- Text input-->
                        <div class="form-group">
                            <label class=" control-label" for="smtp_host">Mail Server</label>
                            <div class="">
                                <input id="smtp_host" name="email[smtp_host]" type="text" value="localhost" class="form-control input-md" required="">
                                <span class="help-block">Enter the name or IP number for the SMTP mail relay server</span>
                            </div>
                        </div>
                        <!-- Text input-->
                        <div class="form-group">
                            <label class=" control-label" for="smtp_user">User</label>
                            <div class="">
                                <input id="smtp_user" name="email[smtp_user]" type="text" value="jhondoe@test.com" class="form-control input-md" required="">
                                <span class="help-block">Enter a valid user name</span>
                            </div>
                        </div>
                        <!-- Password input-->
                        <div class="form-group">
                            <label class=" control-label" for="smtp_pass">Password</label>
                            <div class="">
                                <input id="smtp_pass" name="email[smtp_pass]" type="password" placeholder="" class="form-control input-md">
                                <span class="help-block">Leave it blank if no password</span>
                            </div>
                        </div>
                        <!-- Text input-->
                        <div class="form-group">
                            <label class=" control-label" for="smtp_user_name">From User Name</label>
                            <div class="">
                                <input id="smtp_user_name" name="email[smtp_user_name]" type="text" value="Jhon Doe" class="form-control input-md" required="">
                                <span class="help-block">Enter the name that will appear in from text in mails ie.: Jhon Doe Jr.</span>
                            </div>
                        </div>

                    </fieldset>
            
        </div>
         <!-- Button -->
                        <div class="form-group">
                            <label class=" control-label" for="singlebutton"></label>
                            <div class="">
                                <button type="submit" id="submit" name="submit" class="btn btn-primary">SAVE</button>
                            </div>
                        </div>
    </div>
</form>
</div>
<!-- /container -->