<?php

// link to the font file no the server
$fontname = '../../web/css/fonts/Raleway-Regular.ttf';
// controls the spacing between text
$i=30;
//JPG image quality 0-100
$quality = 90;

$user = [
	array(
			'name'=>  str_replace('.jpg', '',$_GET['name']), 
			'pk'=>  str_replace('.jpg', '',$_GET['PK_EMPLEADO']), 
			'font-size'=>'27',
			'color'=>'green'),
	];
function create_image($user){

		global $fontname;	
		global $quality;
		// $file = "covers/".md5($user[0]['name'].$user[1]['name'].$user[2]['name']).".jpg";	
		$file = $user[0]['pk'].".jpg";	
	
	// if the file already exists dont create it again just serve up the original	
	//if (!file_exists($file)) {	
			

			// define the base image that we lay our text on
			$im = imagecreatefromjpeg("../../web/iconos/correos/cumple.jpg");
			
			// setup the text colours
			$color['grey'] = imagecolorallocate($im, 54, 56, 60);
			$color['green'] = imagecolorallocate($im, 55, 189, 102);
			
			$height=0;
			// this defines the starting height for the text block
			$y = imagesy($im) - $height - 270;
			 
		// loop through the array and write the text	
		$i=0;
		foreach ($user as $value){
			// center the text in our image - returns the x value
			$x = center_text($value['name'], $value['font-size']);	
			// $x = $value['name'], $value['font-size']+5);	
			imagettftext($im, $value['font-size'], 0, $x, $y+$i, $color[$value['color']], $fontname,$value['name']);
			// add 32px to the line height for the next text block
			$i = $i+32;	
			
		}
			// create the image
			imagejpeg($im, $file, $quality);
			
	//}
						
		return $file;	
}

function center_text($string, $font_size){

			global $fontname;

			$image_width = 760;
			$dimensions = imagettfbbox($font_size, 0, $fontname, $string);
			
			return ceil(($image_width - $dimensions[4]) / 2);				
}



// run the script to create the image
$filename = create_image($user);
header("Content-Type: image/jpg");
readfile($filename);

?>