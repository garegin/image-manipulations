<img src="im/beer.JPG" width=300>
<?php
$im = new Imagick("im/beer.JPG");
$exifArray = $im->getImageProperties("exif:GPS*");
$latDMS = $exifArray["exif:GPSLatitude"];
$lonDMS = $exifArray["exif:GPSLongitude"];
// var_dump(explode(",", $latDMS));
list($dlat, $mlat, $slat) = (explode(",", $latDMS));
list($dlon, $mlon, $slon) = (explode(",", $lonDMS));
// echo "dlat:".((int)$dlat)."<br>";
// echo "mlat:".((int)$mlat)."<br>";
// echo "slat:".((int)$slat/100)."<br>";
// var_dump($exifArray);
$latsign = 1;
$lonsign = 1;
if($dlat < 0)  { $latsign = -1; }
if($dlon < 0)  { $lonsign = -1; }

$lat = $latsign * DMStoDEC($dlat, $mlat, $slat);
$lon = $lonsign * DMStoDEC($dlon, $mlon, $slon);
// var_dump($lat);
// var_dump($lon);

//http://maps.googleapis.com/maps/api/geocode/json?latlng=40.1875,44.513889&sensor=false
$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lon."&sensor=false";
$options = array(
                CURLOPT_URL             => $url,
                CURLOPT_POST            => false,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_SSL_VERIFYPEER  => false
            );

$curl = curl_init();
            curl_setopt_array($curl, $options);

            $response = curl_exec($curl);
            $info = curl_getinfo($curl);
            $geolocation = json_decode($response);
            $results = $geolocation->results;
            $locations = [];
            foreach ($results as $key => $locality) {
            	$location["type"] = $locality->types[0];
				$location["name"] = $locality->formatted_address;
				$locations[] = $location;
            }
            var_dump($locations);
            // var_dump($geolocation->results[0]->formatted_address);
            // var_dump($geolocation->results[1]->formatted_address);
            // var_dump(json_decode($response));
            // var_dump($info);


function DMStoDEC($deg,$min,$sec)
{
	// var_dump(calc($deg));
	// var_dump(calc($min));
	// var_dump(calc($sec));
	// echo (46.6/60+7)/60+40;

	return (calc($sec)/60+calc($min))/60+calc($deg);
// Converts DMS ( Degrees / minutes / seconds ) 
// to decimal format longitude / latitude

    return $deg+($min/60+$sec/3600)/1000000;
}



function calc($equation)
{
    // Remove whitespaces
    $equation = preg_replace('/\s+/', '', $equation);
    // echo "$equation\n";

    $number = '(?:-?\d+(?:[,.]\d+)?|pi|π)'; // What is a number
    $functions = '(?:sinh?|cosh?|tanh?|abs|acosh?|asinh?|atanh?|exp|log10|deg2rad|rad2deg|sqrt|ceil|floor|round)'; // Allowed PHP functions
    $operators = '[+\/*\^%-]'; // Allowed math operators
    $regexp = '/^(('.$number.'|'.$functions.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/'; // Final regexp, heavily using recursive patterns

    if (preg_match($regexp, $equation))
    {
        $equation = preg_replace('!pi|π!', 'pi()', $equation); // Replace pi with pi function
        // echo "$equation\n";
        eval('$result = '.$equation.';');
    }
    else
    {
        $result = false;
    }
    return $result;
}
  
?>