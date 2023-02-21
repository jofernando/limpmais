<?php

namespace App\Rules;

use App\Models\Customer;
use App\Models\Duplicata;
use Illuminate\Contracts\Validation\Rule;

class Lancamento implements Rule
{

    private $message = "Formato inválido, verifique os valores inseridos.";
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
            $this->message = "Formato inválido. Verifique se está faltando algum valor entre as vírgulas ou se tem vírgula no fim ou começo. O formato correto é: código do cliente, valor pago, valor comprado.";
            return false;
        }
        foreach ($chunked as $item) {
            if(!is_numeric($item[0]) || !is_numeric($item[1]) || !is_numeric($item[2]))
            {
                $texto = implode(", ", $item);
                $this->message = "Esse texto contém caracters que não são permitidos. {$texto}. Somente números e vírgulas são permitidos.";
                return false;
            }
            if(($item[1] <= 0) || ($item[2] <= 0))
            {
                $texto = implode(", ", $item);
                $this->message = "O valor mínimo para valor pago e valor comprado é 0. Verifique os dados informados: {$texto}.";
                return false;
            }
            $customer = Customer::find($item[0]);
            if ($customer == null) {
                $this->message = "Não existe cliente para o código informando: {$item[0]}";
                return false;
            }
            $divida = $customer->divida;
            if ($item[1] > 0 && $item[1] > $divida) {
                $nome = $customer->nome;
                $this->message = "O cliente {$nome} com código {$item[0]} possui dívida de R\${$divida} mas o valor informando foi R\${$item[1]}. Verifique os dados inseridos.";
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
        return $this->message;
    }
}
