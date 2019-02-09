<html>

<style>
	
	h1 {
		color: blue;
		font-size: 33pt;

	}

	.test{
		width: 200px;
		height: 400px;
		background-color: gray;
	}

</style>
	<body>
		<div class='test'>
			<h1 onclick="sayhallo()"> Hallo World! </h1>

		</div>
		<?php

			for($i = 0; $i < 10; $i++){
				echo "<h2> Hallo ".$i." </h2>";
			}

		?>
	</body>

<script>
	
function sayhallo(){

	for(var i = 0; i < 10; i++){
		alert("Ich bin Nummer: "+i);
	}


}



</script>


</html>