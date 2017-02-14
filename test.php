<?php
$test = '<p style="text-align: center;">94</p>
</td>
</tr>
<tr>
<td>
<p style="text-align: center;">2XL</p>
</td>
<td style="text-align: center;">
<p>76</p>
</td>
<td style="text-align: center;">
<p>72</p>
</td>
<td>
<p style="text-align: center;">96</p>
</td>
</tr>
<tr>
<td>
<p style="text-align: center;"><span style="color: #800000;"><b>Size(Inches)</b></span></p>
</td>
<td style="text-align: center;">
<p><span style="color: #800000;"><b>Waist</b></span></p>
</td>
<td style="text-align: center;">
<p><span style="color: #800000;"><b>Length</b></span></p>
</td>
<td>
<p style="text-align: center;"><span style="color: #800000;"><b>Length</b></span></p>
</td>
</tr>
<tr>
<td>
<p style="text-align: center;">S</p>
</td>
<td style="text-align: center;">
<p>25.20</p>
</td>
<td style="text-align: center;">
<p>26.38</p>
</td>
<td style="text-align: center;">
<p>35.43</p>
</td>
</tr>
<tr>
<td style="text-align: center;">
<p>M</p>
</td>
<td style="text-align: center;">
<p>26.38</p>
</td>
<td style="text-align: center;">
<p>26.38</p>
</td>
<td>
<p style="text-align: center;">35.43</p>
</td>
</tr>
<tr>
<td>
<p style="text-align: center;">L</p>
</td>
<td style="text-align: center;">
<p>27.56</p>
</td>
<td style="text-align: center;">
<p>27.17</p>
</td>
<td style="text-align: center;">
<p>35.83</p>
</td>
</tr>
<tr>
<td style="text-align: center;">
<p>XL</p>
</td>
<td style="text-align: center;">
<p>28.74</p>
</td>
<td style="text-align: center;">
<p>28.35</p>
</td>
<td>
<p style="text-align: center;">37.01</p>
</td>
</tr>
<tr>
<td>
<p style="text-align: center;">2XL</p>
</td>
<td style="text-align: center;">
<p>29.92</p>
</td>
<td style="text-align: center;">
<p>28.35</p>
</td>
<td>
<p style="text-align: center;">37.80</p>
</td>
</tr>
</tbody>
</table>';

//echo strlen($test);

function google_trans($text, $target) {
    $apiKey = 'AIzaSyAYNByJiGLfhiF3HujNKcHkm_KVazOCFkw';
    $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target='.$target;

    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($handle);                 
    $responseDecoded = json_decode($response, true);
    curl_close($handle);

    return $responseDecoded['data']['translations'][0]['translatedText'];
}

$tr = google_trans($test,'it');

echo $tr;

