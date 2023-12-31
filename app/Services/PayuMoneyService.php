<?php


namespace App\Services;


use Illuminate\Support\Facades\Validator;

class PayuMoneyService
{
    public $parameters = array();
    protected $testMode = false;
    protected $merchantKey = '';
    protected $salt = '';
    public $hash = '';
    protected $liveEndPoint = 'https://secure.payu.in/_payment';
    protected $testEndPoint = 'https://sandboxsecure.payu.in/_payment';
    public $response = '';

    public function __construct()
    {
        $this->merchantKey = config('services.payu.key');
        $this->salt = config('services.payu.salt');
        $this->testMode = config('services.payu.mode') == 'sandbox';

        $this->parameters['key'] = $this->merchantKey;
        $this->parameters['txnid'] = $this->generateTransactionID();
        $this->parameters['service_provider'] = 'payu_paisa';
    }

    public function getEndPoint()
    {
        return $this->testMode ? $this->testEndPoint : $this->liveEndPoint;
    }

    /**
     * @throws \Exception
     */
    public function request($parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
        $this->checkParameters($this->parameters);
        $this->encrypt();
        return $this->send();
    }

    /**
     * @return mixed
     */
    public function send()
    {
        return view('front-end.payment.payUform')->with('hash', $this->hash)
            ->with('parameters', $this->parameters)
            ->with('endPoint', $this->getEndPoint());
    }


    /**
     * Check Response
     * @param $request
     * @return array
     */
    public function response($request)
    {
        $response = $request->all();
        $response_hash = $this->decrypt($response);
        if ($response_hash != $response['hash']) {
            return false;
        }
        return $response;
    }

    public function checkParameters($parameters)
    {
        $validator = Validator::make($parameters, [
            'key' => 'required',
            'txnid' => 'required',
            'surl' => 'required|url',
            'furl' => 'required|url',
            'firstname' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'productinfo' => 'required',
            'service_provider' => 'required',
            'amount' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            // throw new \Exception($validator->messages()[0]);
        }
    }

    /**
     * PayUMoney Encrypt Function
     *
     */
    public function encrypt(): string
    {
        $this->hash = '';
        $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";

        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';

        foreach ($hashVarsSeq as $hash_var) {
            $hash_string .= $this->parameters[$hash_var] ?? '';
            $hash_string .= '|';
        }

        $hash_string .= $this->salt;
        $this->hash = strtolower(hash('sha512', $hash_string));
        return $this->hash;
    }

    /**
     * PayUMoney Decrypt Function
     *
     * @param $plainText
     * @param $key
     * @return string
     */
    protected function decrypt($response)
    {

        $hashSequence = "status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key";
        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = $this->salt . "|";
        foreach ($hashVarsSeq as $hash_var) {
            $hash_string .= $response[$hash_var] ?? '';
            $hash_string .= '|';
        }
        $hash_string = trim($hash_string, '|');
        return strtolower(hash('sha512', $hash_string));
    }


    public function generateTransactionID()
    {
        return substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    }
}
