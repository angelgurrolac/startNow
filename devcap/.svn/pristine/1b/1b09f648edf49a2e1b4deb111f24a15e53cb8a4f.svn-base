<?php

// link to the font file no the server
$fontname = '../../web/css/fonts/Raleway-Regular.ttf';
$fontname_bold = '../../web/css/fonts/Raleway-Bold.ttf';
// controls the spacing between text
$i=30;
//JPG image quality 0-100
$quality = 90;

$anios = str_replace('.jpg', '',$_GET['name']);
$user = [
	array(
			//'name'=>  ($anios==1)?'este':$anios, 
			'name'=>'', 
			'font-size'=>'24',
			'color'=>'green'),
	array(
			//'name'=>  '¡Gracias por tu lealtad durante '.(($anios>1)?'estos ':''), 
			'name'=>'', 
			'font-size'=>'24',
			'color'=>'green'),
	array(
			//'name'=>  'años!', 
			'name'=>'', 
			'font-size'=>'24',
			'color'=>'green'),
	array(
			//'name'=>  'año!', 
			'name'=>'', 
			'font-size'=>'24',
			'color'=>'green'),
	];
	// var_dump($user);
function create_image($user){

		global $fontname;	
		global $fontname_bold;	
		global $quality;
		// $file = "covers/".md5($user[0]['name'].$user[1]['name'].$user[2]['name']).".jpg";	
		$file = str_replace('.jpg', '',$_GET['name']).".jpg";	
	
	// if the file already exists dont create it again just serve up the original	
	//if (!file_exists($file)) {	
			

			// define the base image that we lay our text on
			$im = imagecreatefromjpeg("../../web/iconos/correos/aniversario.jpg");
			
			// setup the text colours
			$color['grey'] = imagecolorallocate($im, 54, 56, 60);
			$color['green'] = imagecolorallocate($im, 55, 189, 102);
			
			$height=0;
			// this defines the starting height for the text block
			$y = imagesy($im) - $height - 120;
			 
		// loop through the array and write the text	
		$i=0;
		// foreach ($user as $value){
		// 	// center the text in our image - returns the x value
		// 	$x = center_text($value['name'], $value['font-size']);	
		// 	// $x = $value['name'], $value['font-size']+5);	
		// 	imagettftext($im, $value['font-size'], 0, $x, $y+$i, $color[$value['color']], $fontname,$value['name']);
		// 	imagettftext($im, $value['font-size'], 0, $x, $y+$i, $color[$value['color']], $fontname_bold,$value['name']);
		// 	// add 32px to the line height for the next text block
		// 	$i = $i+32;	
			// $x = center_text($user[0]['name'], $user[0]['font-size']);	
			if($user[0]['name']!='este'){
				$x=55;
				$anios_x = 545;			
				if($user[0]['name'] > 9){
					$anios_x2 = 590;
				} else {
					$anios_x2 = 570;
				}
				$son_anios =2;			
			}else{
				$x=65;
				$anios_x = 455;			
				$anios_x2 = 530;			
				$son_anios =3;
			}
			imagettftext($im, $user[1]['font-size'], 0, $x+0, $y+$i, $color[$user[1]['color']], $fontname,$user[1]['name']);
			imagettftext($im, $user[0]['font-size'], 0, $x+$anios_x, $y+$i, $color[$user[0]['color']], $fontname_bold,$user[0]['name']);
			imagettftext($im, $user[3]['font-size'], 0, $x+$anios_x2, $y+$i, $color[$user[3]['color']], $fontname_bold,$user[$son_anios]['name']);

			// $x = center_text($user[1]['name'], $user[1]['font-size']);	
		// }
			// create the image
			imagejpeg($im, $file, $quality);
			
	//}
						
		return $file;	
}

function center_text($string, $font_size){

			global $fontname;
			global $fontname_bold;

			$image_width = 705;
			$dimensions = imagettfbbox($font_size, 0, $fontname, $string);
			
			return ceil(($image_width - $dimensions[4]) / 2);				
}



// run the script to create the image
$filename = create_image($user);
header("Content-Type: image/jpg");
readfile($filename);

?>