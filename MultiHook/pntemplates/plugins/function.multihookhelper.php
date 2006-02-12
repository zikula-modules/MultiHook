<?php

function smarty_function_multihookhelper($params, &$smarty)
{
    $out = '';
    if(pnSecAuthAction(0, 'MultiHook::', '::', ACCESS_ADD)) { 
        pnModLangLoad('MultiHook', 'admin');
        $pnr = new pnRender('MultiHook', false);
        $out = $pnr->fetch('mh_dynamic_hiddenform.html');
    }
    return $out;
          
}      
?>