<?php

define("VERSION", "1.3b");
define("MY_DEBUG", false);

function gethttp($url){
    $agent = $_SERVER["HTTP_USER_AGENT"];
    $data = urldecode($_GET["opt"]);
    $cookie = urldecode($_GET["cook"]);
    
    $head_chrome = array(
        "Connection: keep-alive",
        "DNT: 1",
        "Upgrade-Insecure-Requests: 1",
        "User-Agent: ".$agent,
        "Sec-Fetch-Dest: document",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
        "Sec-Fetch-Site: none",
        "Sec-Fetch-Mode: navigate",
        "Sec-Fetch-User: ?1",
        "Accept-Encoding: gzip, deflate",
        "Accept-Language: it-IT,it;q=0.9,en-US;q=0.8,en;q=0.7",
        "X-Forwarded-For: ".$_SERVER["REMOTE_ADDR"],
    );
    
    $head_ie10 = array(
        "Accept: text/html, application/xhtml+xml, */*",
        "Accept-Language: it-IT",
        "User-Agent: ".$agent,
        "Accept-Encoding: gzip, deflate",
        "DNT: 1",
        "Connection: Keep-Alive",
        "X-Forwarded-For: ".$_SERVER["REMOTE_ADDR"],
    );
    
    if(preg_match("/Trident/Ui", $agent) == 1){
        $head = $head_ie10;
    } else {
        $head = $head_chrome;
    }
    
    $options = array(
        CURLOPT_CUSTOMREQUEST => ($data == "") ? "GET" : "POST",
        CURLOPT_COOKIEFILE => "",
        CURLOPT_COOKIEJAR => "",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => 1,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTPHEADER => $head,
        CURLOPT_ENCODING => "",
        CURLOPT_AUTOREFERER  => true,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CAPATH => "./cacert.pem",
        CURLOPT_FRESH_CONNECT => true,
        CURLINFO_HEADER_OUT => true,
        /** CURLOPT_PROXY => "ip:port",
        CURLOPT_PROXYUSERPWD => "user:pass",*/
    );
    
    if($data != ""){
        $options[CURLOPT_POSTFIELDS] = $data; // var1=value1&var2=value2&...
    }
    if($cookie != ""){
        $options[CURLOPT_COOKIE] = preg_replace("/&/", ";", $cookie); // var1=value1&var2=value2&...
    }
    
    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $header = curl_getinfo($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    curl_close($ch);
    
    $header["err"] = $err;
    $header["errmsg"] = $errmsg;
    $header["content"] = "\n-------------------------------------------------------------------------------------\n".$content."\n-------------------------------------------------------------------------------------";
    
    return $header;
}

if(key_exists("q", $_GET)){
    header("Content-Type: text/plain");
    if(MY_DEBUG == true){
        print_r($_REQUEST);
    } else {
        print_r(gethttp($_GET["q"]));
    }
    exit();
}
?>
<html>
    <head>
        <title>Custom cUrl <?php echo VERSION; ?></title>
        <style type="text/css">
            body{
                margin: 0px;
                margin-top: 10px;
                cursor: default;
                font-family: monospace;
                text-align: center;
            }
            fieldset{
                margin: 0px;
                padding: 0px;
            }
            legend{
                margin-left: 5px;
            }
            a{
                color: #000000;
                text-decoration: none;
            }
            a:hover{
                color: #FF6600;
                text-decoration: underline;
            }
            input[type=text]{
                border: 1px solid #C0C0C0;
            }
            input[type=text]:hover, input[type=text]:focus{
                border: 1px solid #FF9900;
            }
            table{
                margin-bottom: 20px;
            }
            .credit{
                position: fixed;
                bottom: 0px;
                width: 100%;
                background: #FFFFFF;
                border-top: 1px solid #000000;
                text-align: right;
            }
        </style>
        <script type="text/javascript">
            var nopt = 1;
            var ncook = 1;
            var options = {
                "postvar": 0,
                "cookvar": 0
            };
            function chng(wht){
                var id = document.getElementById(wht);
                if(options[wht] == 0){
                    id.style.display = "initial";
                    options[wht] = 1;
                } else {
                    id.style.display = "none";
                    options[wht] = 0;
                }
                console.log(wht.toUpperCase(), options[wht]);
            }
            function addpost(){
                var x = document.getElementById("container");
                
                var tr = document.createElement("tr");
                var nome = document.createElement("td");
                var value = document.createElement("td");
                var inome = document.createElement("input");
                var ivalue = document.createElement("input");
                inome.type = "text";
                inome.id = "n_post"+((++nopt).toString());
                inome.style = "width: 100%";
                ivalue.type = "text";
                ivalue.id = "v_post"+((nopt).toString());
                ivalue.style = "width: 100%";
                nome.appendChild(inome);
                value.appendChild(ivalue);
                tr.appendChild(nome);
                tr.appendChild(value);
                x.appendChild(tr);
            }
            function addcook(){
                var x = document.getElementById("contcook");
                
                var tr = document.createElement("tr");
                var nome = document.createElement("td");
                var value = document.createElement("td");
                var inome = document.createElement("input");
                var ivalue = document.createElement("input");
                inome.type = "text";
                inome.id = "n_cook"+((++ncook).toString());
                inome.style = "width: 100%";
                ivalue.type = "text";
                ivalue.id = "v_cook"+((ncook).toString());
                ivalue.style = "width: 100%";
                nome.appendChild(inome);
                value.appendChild(ivalue);
                tr.appendChild(nome);
                tr.appendChild(value);
                x.appendChild(tr);
            }
            function senddata(){
                var form = document.getElementById("form");
                var url = document.getElementById("url");
                var g = "";
                var h = "";
                
                if(url.value == ""){
                    alert("NESSUN URL INSERITO");
                    return false;
                }
                
                if(options["postvar"] == 1){
                    for(var i = 0; i < nopt; i++){
                        if(document.getElementById("n_post"+(i+1).toString()).value != ""){
                            g += ((i > 0) ? '&' : '')+(document.getElementById("n_post"+(i+1).toString()).value)+"="+(document.getElementById("v_post"+(i+1).toString()).value);
                        }
                    }
                    document.getElementById("hiddenpost").value = encodeURI(g);
                } else {
                    document.getElementById("hiddenpost").value = "";
                }
                
                if(options["cookvar"] == 1){
                    for(var k = 0; k < ncook; k++){
                        if(document.getElementById("n_cook"+(k+1).toString()).value != ""){
                            h += ((k > 0) ? '&' : '')+(document.getElementById("n_cook"+(k+1).toString()).value)+"="+(document.getElementById("v_cook"+(k+1).toString()).value);
                        }
                    }
                    document.getElementById("hiddencook").value = encodeURI(h);
                } else {
                    document.getElementById("hiddencook").value = "";
                }
                
                form.submit();
                
            }
            window.onload = function(){
                document.getElementById("url").focus();
            }
        </script>
    </head>
    <body>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get" target="_blank" enctype="multipart/form-data" id="form">
            <center>
                <table width="70%">
                    <tr>
                        <td>
                            <b>URL</b>
                        </td>
                        <td>
                            <input type="text" value="" name="q" id="url" style="width: 100%" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>OPTIONS</b>
                        </td>
                        <td>
                            <center>
                                <input type="checkbox" onclick="chng('postvar')" id="opt" /><b>Post?</b> - <input type="checkbox" onclick="chng('cookvar')" id="cook" /><b>Cookie?</b>
                            </center>
                        </td>
                    </tr>
                </table>
                <br />
                <input type="button" value="GO!" onclick="senddata()" />
                <div>
                    <div id="cookvar" style="display: none; float: left; width: 50%;">
                        <fieldset style="width: 100%; text-align: left;">
                            <legend><a href="javascript: void();" onclick="addcook()">COOKIE +</a></legend>
                            <table width="100%" id="contcook">
                                <tr>
                                    <td style="text-align: center;">
                                        <b>NOME VAR</b>
                                    </td>
                                    <td style="text-align: center;">
                                        <b>VALUE</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" value="" id="n_cook1" style="width: 100%" />
                                    </td>
                                    <td>
                                        <input type="text" value="" id="v_cook1" style="width: 100%" />
                                    </td>
                                </tr>
                            </table>
                            <input type="hidden" value="" name="cook" id="hiddencook" />
                        </fieldset>
                    </div>
                    <div id="postvar" style="display: none; float: right; width: 50%;">
                        <fieldset style="width: 100%; text-align: left;">
                            <legend><a href="javascript: void();" onclick="addpost()">POST +</a></legend>
                            <table width="100%" id="container">
                                <tr>
                                    <td style="text-align: center;">
                                        <b>NOME VAR</b>
                                    </td>
                                    <td style="text-align: center;">
                                        <b>VALUE</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" value="" id="n_post1" style="width: 100%" />
                                    </td>
                                    <td>
                                        <input type="text" value="" id="v_post1" style="width: 100%" />
                                    </td>
                                </tr>
                            </table>
                            <input type="hidden" name="opt" value="" id="hiddenpost" />
                        </fieldset>
                    </div>
                </div>
            </center>
        </form>
        <div class="credit">
            <div style="margin-right: 10px;">Powered by <a href="http://raf92.altervista.org/" target="_blank">Mrphp</a></div>
        </div>
    </body>
</html>