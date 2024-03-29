<?php

namespace App\Rules;

use App\Models\Cliente;
use Illuminate\Contracts\Validation\Rule;

class Impressao implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $exploded = explode(',', $value);
        $chunked = array_chunk($exploded, 3);
        if (count(end($chunked)) != 3) {
            $this->message = "Formato inválido. Verifique se está faltando algum valor entre as vírgulas ou se tem vírgula no fim ou começo. O formato correto é: número do cliente, valor recebido, valor a receber.";
            return false;
        }
        foreach ($chunked as $item) {
            if(!is_numeric($item[0]) || !is_numeric($item[1]) || !is_numeric($item[2]))
            {
                $texto = implode(", ", $item);
                $this->message = "Esse texto contém caracters que não são permitidos. {$texto}. Somente números, vírgulas e o sinal de menos são permitidos.";
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
