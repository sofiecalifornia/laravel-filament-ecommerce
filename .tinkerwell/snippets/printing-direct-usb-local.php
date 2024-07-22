<?php


use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

try {
    $connector = new FilePrintConnector("/dev/usb/lp2");
    $printer = new Printer($connector);
    $printer->text("Hello World!".PHP_EOL);
    $printer->cut();

    $printer->close();

    echo "ok";
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e->getMessage() . PHP_EOL;
}


# https://github.com/mike42/escpos-php/blob/development/example/interface/linux-usb.php
# https://devicetests.com/fix-permission-denied-error-usb-devices-ubuntu

/**
 * On Linux, use the usblp module to make your printer available as a device
 * file. This is generally the default behaviour if you don't install any
 * vendor drivers.
 *
 * Once this is done, use a FilePrintConnector to open the device.
 *
 * Troubleshooting: On Debian, you must be in the lp group to access this file.
 * dmesg to see what happens when you plug in your printer to make sure no
 * other drivers are unloading the module.
 */
