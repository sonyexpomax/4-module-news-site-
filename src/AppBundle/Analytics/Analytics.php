<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 06.02.18
 * Time: 14:02
 */

namespace AppBundle\Analytics;

use AppBundle\Entity\User;

class Analytics
{
    private $input;
    private $user;

    public function setNews($input, $user)
    {
        $this->input = $input;
        $this->user = $user;
    }

    public function getResult()
    {

        if($this->input['isAnalytic'] && !$this->user){
            preg_match_all(
                "/([А-Яа-яЁёA-Za-z\ \,0-9\:\)\(\«\»\-\'\"\;]+)/u",
                $this->input['description'],
                $out);

            $e = [];
            foreach ($out[0]  as $key=>$value){
                if($key < 5){
                    $e[] = $value;
                }
                else{
                    break;
                }
            }

            $result = implode(' ', $e);
            $this->input['description'] = $result ;

             return $this->input;
        }

        $this->input['dfsdsf']=554;
        return $this->input;
    }

}