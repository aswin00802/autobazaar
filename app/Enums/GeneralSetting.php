<?php 
namespace app\Enums;

use Illuminate\Validation\Rules\Enum;
final class GeneralSetting extends Enum{

    const active = 1;
    const in_active = 0;
}
