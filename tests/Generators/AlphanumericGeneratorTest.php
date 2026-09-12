<?php

use Itsmurumba\Otp\Generators\AlphanumericGenerator;

test('it generates an alphanumeric OTP of specified length', function () {
    $generator = new AlphanumericGenerator();
    $otp = $generator->generate(6);
    
    expect($otp)
        ->toBeString()
        ->toHaveLength(6)
        ->toMatch('/^[0-9A-Z]+$/');
});

test('it validates alphanumeric OTPs correctly', function () {
    $generator = new AlphanumericGenerator();
    
    expect($generator->validate('A1B2C3'))->toBeTrue();
    expect($generator->validate('12345'))->toBeFalse(); // Too short
    expect($generator->validate('A1B2C3D'))->toBeFalse(); // Too long
    expect($generator->validate('12345@'))->toBeFalse(); // Contains special character
    expect($generator->validate(''))->toBeFalse(); // Empty
});

test('it validates against an explicit expected length', function () {
    $generator = new AlphanumericGenerator();

    expect($generator->validate('AB12', 4))->toBeTrue();
    expect($generator->validate('A1B2C3', 4))->toBeFalse();
}); 