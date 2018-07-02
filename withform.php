<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- style -->
      <!-- <script type="text/javascript" src="assets/js/jquery-3.3.1.min.js"></script> -->
      <script type="text/javascript" src="assets/js/jquery.js"></script>
    	<script src="assets/js/bootstrap.min.js"></script>
    	<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <!-- style -->
    <title>Document</title>
  </head>

  <body>
    <br>
    <div class="container">
      <form onsubmit="saveform();return false;">

        <div class="form-group">
          <label>Number</label>
          <input required name="number" id="number" onkeyup="waGenerator();" type="text" class="form-control" aria-describedby="notification" placeholder="no. (ex: 08xxx,02xxx,09xxx, etc.)">
          <!-- <input required name="number" min="0" max="12" id="number" onkeyup="waGenerator();" type="number" class="form-control" aria-describedby="notification" placeholder="no. (ex: 08XXX)"> -->
        </div>

        <div class="form-group">
          <label>Message</label>
          <textarea required onkeyup="waGenerator();" name="message" type="text" class="form-control" id="message"  placeholder="message here...."></textarea>
        </div>

		<p id="generatedUrl">
			<!-- link here -->
		</p>
		<!-- <a href="#" class="btn btn-secondary" onclick="copyToClipBoard()">copy link</a> -->

        <button type="submit" class="btn btn-primary">Submit</button>
      </form>

    </div>
  </body>

  <script>
    $(document).ready(function(){
      // code here
    });

    function copyToClipBoard() {
		// /* Get the text field */
		// var copyText = document.getElementById("#generatedUrl");

		// /* Select the text field */
		// copyText.select();

		// /*Copy the text inside the text field*/ 
		// document.execCommand("copy");

		// /* Alert the copied text */
		// alert("Copied the text: " + copyText.value);
	} 

	function waGenerator(){
		var number = $('#number').val();
		var message = $('#message').val();
		$.ajax({
			url:'process.php',
			data:{
				'mode':'phoneConvert',
				'number':number
			},type:'post',
			dataType:'json',
			success:function(ret){
				var generatedText='';
				if(ret.number=='unknown'){ // valid number (right digit & prefix)
					generatedText ='<span style="color:red;">please check again, your number is invalid</span>';
				} else { // invalid number 
					var urlx = 'https://wa.me/'+ret.number+'/?text='+encodeURI(message);
					generatedText = '<a target="_blank" href="'+urlx+'">'+urlx+'</a>';
				}
				$('#generatedUrl').html(generatedText);
	        }, error : function (xhr, status, errorThrown) {
	            console.log('['+xhr.status+'] '+errorThrown);
	        }
      });
    }

    function saveform(){
        // var urlx ='&mode=phonesave';
        // $.ajax({
        //     url:'process.php',
        //     cache:false,
        //     type:'post',
        //     dataType:'json',
        //     data:$('form').serialize()+urlx,
        //     success:function(dt){
        //     	// console.log(dt.status);
        //       if(dt.status==false){
        //       	alert('Gagal menyimpan data');
        //       }else{
        //         resetform();
        //       	alert('Berhasil menyimpan data');
        //       }
        //     }
        // });
    }

	    function resetform() {
      //   $('#number').focus();
      //   $('#country').val('');
	    	// $('#number').val('');
	    	// $('#number_new').val('');
	    }
  </script>
</html>
