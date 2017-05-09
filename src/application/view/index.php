<!-- body -->
 <body>
   <div class="row" style="margin-top:2%;">
     <div class="col-md-4"></div>
     <div class="col-md-4"><img class="img-responsive center-block" src="http://javamon.be/oversearch/images/overwatch_logo.png" alt="OverSe" id="logo"/></div>
     <div class="col-md-4"></div>
   </div>
   <div class="row" style="margin-top:2%;">
     <div class="col-md-4"></div>
     <div class="col-md-4">
       <!-- form -->
       <form class="form-inline" name="myForm" method="post" onsubmit="return false;">
         <label for="nickname"><strong>닉네임#배틀태그를 입력해주세요.</strong></label><br>
         <input type="text" class="form-control" id="user" name="user" placeholder="닉네임#TAG" value="오버워치#35923" required>
         <button class="btn btn-warning" type="submit" onclick="validateForm();">검색</button>
       </form>
     </div>
     <div class="col-md-4"></div>
   </div>
   <!-- footer -->
   <div id="footer"></div>
   <script>
   var url = "<?=$host."main/view/user/".$user?>";
   function validateForm()
   {
       var input = document.forms["myForm"]["user"].value;
       var chk = input.match(/(.)+#[0-9]{4,5}/g);
       if (input == null
           || input == ""
           || chk != document.forms["myForm"]["user"].value
           || chk == ""
           || chk == null)
           {
           alert("입력한 배틀태그를 확인해주세요.");
           return false;
       }
       else
       {
            var user = $("#user").val().replace("#", "-");
            var to_url = url+user;
            $(location).attr('href', to_url);
       }
   }
    </script>