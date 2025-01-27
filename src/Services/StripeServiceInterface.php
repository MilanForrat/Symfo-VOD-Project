<?php

namespace App\Services;

interface StripeServiceInterface{

    public function payment($panier, $id_order):string;

    public function getSessionId():mixed;

    public function getSessionOrder():mixed;

}