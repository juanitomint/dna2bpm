<!-- HEADER -->
<div id="header_wrap" class="text-center">
    <header class="inner">
        <h1 id="project_title">{reader_title}</h1>
        <h2 id="project_tagline">{reader_subtitle}</h2>
    </header>
</div>

<!-- MAIN CONTENT -->
<div id="main_content_wrap" class="text-center">
    <section id="main_content">


        <h3>QR code Reader Demo</h3>
        <div  class="img-polaroid" id="reader"></div>
        <h6 >Result</h6>
        <span id="read" ></span>
        <span id="result" ></span>
        <br>
        <form action="{redir}" id="formqr" name="formqr" method="POST"> <input type="text" id="readHidden" name="readHidden"/> </form>
        <h6 >Read Error (Debug only)</h6>
        <span id="read_error" ></span>

        <br>
        <h6 >Video Error</h6>
        <span id="vid_error" ></span>

        <br>
        <br>         
        <br>

        </div>
    </section>
    <!-- FOOTER  -->
    <div id="footer_wrap" class="outer">
        <footer class="inner">

        </footer>
    </div>