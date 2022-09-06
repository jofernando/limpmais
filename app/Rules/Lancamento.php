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
            $this->message = "Formato inválido. Verifique se está faltando algum valor entre as vírgulas ou se tem vírgula no fim ou começo. O formato correto é: número do cliente, valor recebido, valor a receber.";
            return false;
        }
        foreach ($chunked as $item) {
            if(!is_numeric($item[0]) || !is_numeric($item[0]) || !is_numeric($item[0]))
            {
                $texto = implode(", ", $item);
                $this->message = "Esse texto contém caracters que não são permitidos. {$texto}. Somente números, vírgulas e o sinal de menos são permitidos.";
                return false;
            }
            $customer = Customer::find($item[0]);
            $duplicatas = $customer->duplicatas()->where('quitada', false)->get();
            $divida = $duplicatas->map(fn($duplicata) => $duplicata->valor)->sum();
            if ($item[1] != '0' && $item[1] > $divida) {
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
