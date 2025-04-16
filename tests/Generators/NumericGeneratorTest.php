<?php

use Itsmurumba\Otp\Generators\NumericGenerator;

test('it generates a numeric OTP of specified length', function () {
    $generator = new NumericGenerator();
    $otp = $generator->generate(6);
    
    expect($otp)
        ->toBeString()
        ->toHaveLength(6)
        ->toMatch('/^[0-9]+$/');
});

test('it validates numeric OTPs correctly', function () {
    $generator = new NumericGenerator();
    
    expect($generator->validate('123456'))->toBeTrue();
    expect($generator->validate('12345'))->toBeFalse(); // Too short
    expect($generator->validate('12345a'))->toBeFalse(); // Contains non-numeric
    expect($generator->validate(''))->toBeFalse(); // Empty
}); 