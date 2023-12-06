<?php
if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'GET' ) {
    http_response_code( 400 );
    echo json_encode( [ 'error' => 'Invalid request method' ] );
    exit();
}
$params = [ /*'size', */'theme', 'title', 'description' ];
foreach ( $params as $param ) {
    if ( !isset( $_GET[ $param ] ) || empty( $_GET[ $param ] ) ) {
        http_response_code( 400 );
        echo json_encode( [ 'error' => 'Invalid parameters' ] );
        exit();
    }
}
$size = filter_var( $_GET[ 'size' ], FILTER_VALIDATE_INT );
$theme = filter_var( $_GET[ 'theme' ], FILTER_SANITIZE_STRING );
$title = filter_var( $_GET[ 'title' ], FILTER_SANITIZE_STRING );
$sanitizedTitle = strtolower(str_replace(' ', '_', $title));
$description = filter_var( $_GET[ 'description' ], FILTER_SANITIZE_STRING );
//if ( !$size || !in_array( $size, [ 1 ] ) ) {
//    http_response_code( 400 );
//    echo json_encode( [ 'error' => 'Invalid size parameter' ] );
//    exit();
//}
$themes = [ 'classic', 'gradient', 'classic-light', 'gradient-light', 'classic-dark', 'gradient-dark' ];
if ( !in_array( $theme, $themes ) ) {
    http_response_code( 400 );
    echo json_encode( [ 'error' => 'Invalid theme' ] );
    exit();
}
if ( strlen( $title ) > 32 ) {
    http_response_code( 400 );
    echo json_encode( [ 'error' => 'Title exceeds maximum length' ] );
    exit();
}
if ( strlen( $description ) > 32 ) {
    http_response_code( 400 );
    echo json_encode( [ 'error' => 'Description exceeds maximum length' ] );
    exit();
}
if ( $theme === 'classic' || $theme === 'classic-light' || $theme === 'classic-dark' ) {
    if ( $theme === 'classic-dark' ) {
        $imagePath = '../assets/img/classic/dark.png';
    } else {
        $imagePath = '../assets/img/classic/light.png';
    }
} elseif ( $theme === 'gradient' || $theme === 'gradient-light' || $theme === 'gradient-dark' ) {
    if ( $theme === 'gradient-dark' ) {
        $imagePath = '../assets/img/gradient/dark.png';
    } else {
        $imagePath = '../assets/img/gradient/light.png';
    }
} else {
    echo json_encode( [ 'status' => false, 'error' => 'Invalid theme' ] );
    exit();
}
$forceDownload = isset( $_GET[ 'download' ] ) && $_GET[ 'download' ] === 'true';
$image = imagecreatefrompng( $imagePath );
$titleColor = ( $theme === 'classic' || $theme === 'classic-light' ) ? imagecolorallocate( $image, 30, 30, 30 ) : imagecolorallocate( $image, 255, 255, 255 );
$descriptionColor = ( $theme === 'classic' || $theme === 'classic-light' ) ? imagecolorallocate( $image, 62, 62, 62 ) : imagecolorallocate( $image, 212, 212, 212 );
imagettftext( $image, 48, 0, 48, 92, $titleColor, '../assets/font/title.ttf', $title );
imagettftext( $image, 26, 0, 48, 138, $descriptionColor, '../assets/font/description.ttf', $description );
header( 'Content-Type: image/png' );
if ( $forceDownload ) {
    header( 'Content-Disposition: attachment; filename="' . $sanitizedTitle . '.png"' );
}
imagepng( $image );
imagedestroy( $image );
?>