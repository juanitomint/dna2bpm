<!-- HEADER -->
<div id="header_wrap" class="text-center">
    <header class="inner">

        <h1 id="project_title"><img  class="img-polaroid" src="{module_url}gen_url/{module_url_encoded}/6/L" width="160" height="160"/> {title}</h1>
    </header>
</div>

<!-- MAIN CONTENT -->
<div id="main_content_wrap" class="text-center">
    <section id="main_content">
        <ul class="nav nav-pills nav-stacked">
            <li>

                <a href="qr/get_demo">
                    Demo Read
                </a>

            </li>
            <li>

                <a href="qr/gen_demo">
                    Demo gen 
                </a>
            </li>
            <li>
                <form class="form-horizontal" action="{module_url}gen_url" method="POST" name="formgen" id="formgen">
                    <legend>Demo gen url</legend>

                    <div class="controls">
                        Get with encoded url base64_encode("{module_url}"):<br/>
                        <a href="{module_url}gen_url/aHR0cDovL2xvY2FsaG9zdC9kbmEyYnBtL3Fy"> {module_url}gen_url/aHR0cDovL2xvY2FsaG9zdC9kbmEyYnBtL3Fy </a>
                        <br/>
                        <br/>
                        <input type="text" id="url" name="url" value="{base_url}user/profile/edit" >
                        <button type="submit" class="btn">Submit</button>
                    </div>
                </form>
            </li>
            <li>
                <form class="form-horizontal" action="{module_url}test_encode" method="POST" name="formgen" id="formgen">
                    <legend>Encode url</legend>

                    <div class="controls">
                        <input type="text" id="url" name="url" value="{module_url}" >
                        <button type="submit" class="btn">Submit</button>
                    </div>
                </form>
            </li>
            <li>
                <a href="qr/gen_vcard">
                    Demo Vcard
                </a>
            </li>
            <li>
                <form class="form-horizontal" action="{module_url}Read_demo_form" method="POST" name="form1" id="form1">
                    <legend>Demo redir Form</legend>
                    <div class="controls">
                        <input type="text" id="redir" name="redir" value="{base_url}user/profile/edit" >
                        <button type="submit" class="btn">Submit</button>
                    </div>
                </form>   

            </li>
            <li>
                <form class="form-horizontal" action="{module_url}read_demo" method="POST" name="formAjax" id="formAjax">
                    <legend>Demo redir Ajax</legend>
                    <div class="controls">
                        <input type="text" id="redir" name="redir" value="{module_url}dummy" >
                        <button type="submit" class="btn">Submit</button>
                    </div>
                </form>   

            </li>
        </ul>
        <br/>
        <br/>
        <div class="alert span4" >
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Warning!</strong> Make sure qr/cache and qr/log has read/write permissions
        </div>
    </section>
</div>
<!-- FOOTER  -->
<div id="footer_wrap" class="outer">
    <footer class="inner">

    </footer>
</div>