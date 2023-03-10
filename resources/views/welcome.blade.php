<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>TUW. Under Construction</title>
</head>
<style>
	/*@import url('https://fonts.googleapis.com/css2?family=Saira:wght@100;200;300;400;500;600;700;800;900&display=swap');*/
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap');
	html, body{height:100%; width:100%;font-family: 'Poppins', sans-serif;}
	/*img{max-width:95%;}*/
	/*.left-content{width: 500px;}*/
	.dtls{text-align: center;}
	.dtls h1{color: #4f5e55;font-size: 35px;text-transform: uppercase;line-height: 60px;font-weight: 400;margin: 0;}
	.dtls h5{margin: 0;font-size: 15px;font-weight: 400;color: #000;margin: 15px 0;}
	.dtls p{font-size: 15px;line-height: 30px;color: #7e7e7e;font-weight: 300;text-align: justify;margin-bottom: 30px;}
	.phone{display: flex;align-items: center;padding-bottom: 10px;}
	/*.mail{display: flex;align-items: center;;padding-bottom: 10px;}*/
	h6{margin: 0;padding-left: 15px;font-size: 15px;font-weight: 400;color: #757575;}
	a{text-decoration: none;}
	.location{display: flex;align-items: flex-start;}
	.right-content img{width: 100%;height: 100%;}

	@media (max-width: 1200px) {
		/*.left-content{width: auto;}*/
		.dtls h1{
		  font-size: 35px;
		  line-height: 40px;
		}
	}
	@media (max-width: 930px) {
		.dtls h1{font-size: 30px;}
		.right-content img{object-fit: contain;}
	}
	@media (max-width: 680px) {
		.right-content{display: none;}
		.logo img{width: 170px;}
	}
	/*@media (max-width: 576px) {
		.right-content{display: none;}
	}*/
</style>
<body style="background-color:#f2f6ff; margin:0; padding:0; height:100%;">

	<div style="display:flex; justify-content:center; align-items:center; height:100%;">

		<div class="content">
			
			<div class="left-content">

				<div class="dtls">
					<div class="logo"><img src="{{ asset('img/app-logo-02.png') }}"></div>	
					<h1>Coming Soon</h1>
					<a href="mailto:info@tuw.com"><div class="mail"><h6>info@tuw.com</h6></div></a>
				</div>
				
			</div>
		</div>
    </div>

</body>
</html>
