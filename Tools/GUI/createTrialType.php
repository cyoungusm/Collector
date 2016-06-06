<?php

/*
  	GUI - Trial type editor by Anthony Haffey

	Collector
    A program for running experiments on the web
    Copyright 2012-2015 Mikey Garcia & Nate Kornell

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>

    */

    // retrieving elements in readable form
    $newTrialTypeInfo=file_get_contents("GUI/newTrialTypes/".$_DATA['trialTypeEditor']['currentTrialTypeName'].".txt");
    $newTrialTypeInfo=json_decode($newTrialTypeInfo);
    if(isset($newTrialTypeInfo->elements)){ // to deal with retrieval as an array or as an object
      $newTrialTypeArray=$newTrialTypeInfo->elements;
    } else {
      $newTrialTypeArray=$newTrialTypeInfo['elements'];
    }
    if(isset($newTrialTypeInfo->responses)){
      $responseElements=$newTrialTypeInfo->responses;
    } 
    /*
    else {
      $responseElements=$newTrialTypeInfo['responses'];      
    }
    */
    $newTrialHtmlCode = '
    <textarea id="response" name="Response" placeholder="your responses will go here!"></textarea>
    
    
    '; //declaring the $newTrialCode
    
        
    // reading each of the html for each element    
    foreach($newTrialTypeArray as $newTrialTypeElement){
      if(isset($newTrialTypeElement->onsetTime)){
        $initialDisplay = "display:none;";
      } else {
        $initialDisplay = "";
      }
      
      $elementValue='';
      if(isset($newTrialTypeElement->responseValue)){
        $elementValue='data-value="'.$newTrialTypeElement->responseValue.'"';
      }
      
      /* no need for this if using jquery
      if($newTrialTypeElement->clickOutcomesAction!=''){
        $clickElement='onclick="'.$newTrialTypeElement->elementName.'Click()"';
      }
      */
      
      if($newTrialTypeElement->trialElementType=="media"){

        if($newTrialTypeElement->mediaType=="Pic"){
          $newTrialHtmlCode=$newTrialHtmlCode.'  
          <img id ="'.$newTrialTypeElement->elementName.'"
            src=<?= "../Experiments/Common/'.$newTrialTypeElement->stimulus.'" ?> 
            ' .$elementValue. '            
            style=" '.$initialDisplay.'
            position:absolute;
            width:'.$newTrialTypeElement->width.'%;
            height:'.$newTrialTypeElement->height.'%;
            left:'.$newTrialTypeElement->xPos.'%;
            top:'.$newTrialTypeElement->yPosition.'%;
            z-index:'.$newTrialTypeElement->zPosition.';
          ">';
        }
        if($newTrialTypeElement->mediaType=="Video"){
          $newTrialHtmlCode=$newTrialHtmlCode.'  
          <iframe id ="'.$newTrialTypeElement->elementName.'"
            ' .$elementValue. '          
            style=" '.$initialDisplay.'
            position:absolute;
            left:'.$newTrialTypeElement->xPos.'%;
            top:'.$newTrialTypeElement->yPosition.'%;
            z-index:'.$newTrialTypeElement->zPosition.';"
            width="'.$newTrialTypeElement->width.'%" 
            height="'.$newTrialTypeElement->height.'%" 
            frameborder="0"
            webkitallowfullscreen mozallowfullscreen allowfullscreen
            src=<?= "../Experiments/Common/'.$newTrialTypeElement->stimulus.'"?>
            >
          </iframe>';
        }
        if($newTrialTypeElement->mediaType=="Audio"){
          $newTrialHtmlCode=$newTrialHtmlCode.'  
          <audio id ="'.$newTrialTypeElement->elementName.'" src="<?= "../Experiments/Common/'.$newTrialTypeElement->stimulus.'"?>" autoplay>
          </audio>';
        }            
      }
      
      //standardising sizes
      if(isset($newTrialTypeElement->textSize)){
        $newTrialTypeElement->textSize=3*$newTrialTypeElement->textSize;
      }
      
      
      if(strcmp($newTrialTypeElement->trialElementType,"text")==0){
        $newTrialHtmlCode=$newTrialHtmlCode.'<div id ="'.$newTrialTypeElement->elementName.'" 
          ' .$elementValue. '
          style=" '.$initialDisplay.'
          position:absolute;
          width:'.$newTrialTypeElement->width.'%;
          height:'.$newTrialTypeElement->height.'%;
          left:'.$newTrialTypeElement->xPos.'%;
          top:'.$newTrialTypeElement->yPosition.'%;
          z-index:'.$newTrialTypeElement->zPosition.';
          font-size:'.$newTrialTypeElement->textSize.'px;
          color:'.$newTrialTypeElement->textColor.';
          background-color:'.$newTrialTypeElement->textBack.';
          font-family:'.$newTrialTypeElement->textFont.';            
          "><?= "'.$newTrialTypeElement->stimulus.'"; ?></div>'; //if no $ is present. Or maybe even if a dollar is present
          
      }
      if($newTrialTypeElement->trialElementType=="input"){
        $newTrialHtmlCode=$newTrialHtmlCode.'<input id ="'.$newTrialTypeElement->elementName.'" 
          ' .$elementValue. '
          
          type="'.$newTrialTypeElement->userInputType.'"
          
          style=" '.$initialDisplay.'
          position:absolute;
          width:'.$newTrialTypeElement->width.'%;
          height:'.$newTrialTypeElement->height.'%;
          left:'.$newTrialTypeElement->xPos.'%;
          top:'.$newTrialTypeElement->yPosition.'%;
          z-index:'.$newTrialTypeElement->zPosition.';
          font-size:'.$newTrialTypeElement->textSize.'px;
          color:'.$newTrialTypeElement->textColor.';
          background-color:'.$newTrialTypeElement->textBack.';
          font-family:'.$newTrialTypeElement->textFont.';
          " value= "'.$newTrialTypeElement->stimulus.'";   />'; //if no $ is present. Or maybe even if a dollar is present

      }
    }
      // Javascript code is being added here
      $newTrialJSCode="<script>";
      
          foreach($newTrialTypeArray as $newTrialTypeElement){
            $jsAction='';
            $respPos='';
            $jsResp='';
            $jsProc='';
            
            if($newTrialTypeElement->clickOutcomesAction!=''){
              //$clickElement='onclick="'.$newTrialTypeElement->elementName.'Click()"';
              
              /* Click Actions */
              // list files in clickActions folder
              $clickActions = glob('GUI/clickActionsBackend/*.php');
              foreach ($clickActions as $clickAction){
                require ("$clickAction");
              }
              
                         
              
              
              /* Response processing */
//              $responseElements=json_decode($responseElements);
              /*
              print_r($responseElements[0][0]);
              echo "br".count($responseElements)."<br>";
              */
              for($i=0;$i<count($responseElements);$i++){
                 // echo "<br> here<br>";
                
                if(in_array($newTrialTypeElement->elementName,$responseElements[$i])){
                  
                  //echo "<br> here<br>";
                  // code to add to form
                  //identify position of element in array
//                  $respPos=array_search($newTrialTypeElement->elementName,$responseElements);
                  $jsResp="respArray[$i]=".'$("#'.$newTrialTypeElement->elementName.'").data("value");
                  updateResp();';                
                }                
              }
              
              
              

              
            }
            /* Proceed elements */
            if($newTrialTypeElement->proceed=="true"){
              $jsProc="$('form').submit();";
            }

            $newTrialJSCode=$newTrialJSCode.'
            $("#'.$newTrialTypeElement->elementName.'").click(function(){
              '.$jsAction.'
              '.$jsResp.'
              '.$jsProc.'
            });'; 

          }
          
          //currently handling onset and offsets separately - for simplicity
          $jsOnsetCode='';
          foreach($newTrialTypeArray as $newTrialTypeElement){
            if(isset($newTrialTypeElement->onsetTime)){
              $splitOnset=explode(":",$newTrialTypeElement->onsetTime);
              $onsetMS=24*60*1000*60000*$splitOnset[0]+60*1000*$splitOnset[1]+1000*$splitOnset[2];
              if (isset($splitOnset[3])){ //ms data is not always present
                $onsetMS+$splitOnset[3];                
              }
              //$newTrialTypeElement->onsetTime=DurationFormatUtils.formatDuration($newTrialTypeElement->onsetTime, "SSS");
              //time($newTrialTypeElement->onsetTime);
              $jsOnsetCode=$jsOnsetCode.'
              $("#'.$newTrialTypeElement->elementName.'").delay('.$onsetMS.').fadeIn(0);
              ';
            }
   //         $("#element1").delay(1500).fadeIn(0);
          
          
          }
          $newTrialJSCode=$newTrialJSCode.$jsOnsetCode;

          

      //record keyboard responses here

      $keyboardResponse='';
      if(!empty($newTrialTypeInfo->keyboard->acceptedResponses)){
        $keyboardResponse = "
        $(window).bind('keydown', function(event) {
          switch (String.fromCharCode(event.which).toLowerCase()) {";
          for($i = 0; $i<strlen($newTrialTypeInfo->keyboard->acceptedResponses); $i++){
            $currentKey=$newTrialTypeInfo->keyboard->acceptedResponses[$i];
            $keyboardResponse = $keyboardResponse."
            case '$currentKey':
                event.preventDefault(); // not sure this is working in firefox
                response.value='$currentKey'; // change response //";
                if($newTrialTypeInfo->keyboard->proceed="true"){
                  $keyboardResponse=$keyboardResponse."
                  $('form').submit();
                  ";
                }
                
                "
                //proceed trial if keyboard response is meant to do so
                
                break;            
            ";
          }       
      $keyboardResponse = $keyboardResponse."}
          });";

      /* Steps:
        -add "keyboard responses" to newTrialTypeInfo object
        -go through each character separately.
          -check whether it's in the "correct keyboard response"
          
        
      /*
      
      "textSize": 12,
      "textColor": "default",
      "textFont": "default"
      */






       
      }
      
      $newTrialJSCode=$newTrialJSCode.$keyboardResponse."
        respArray=[];
        function updateResp(){
          response.value=respArray;
        }
        </script>";
      
      $newTrialCode=$newTrialHtmlCode.$newTrialJSCode;
      if (!file_exists("../Experiments/Common/TrialTypes/".$_DATA['trialTypeEditor']['currentTrialTypeName'])){
        //nothing happens
      //} else {
        mkdir("../Experiments/Common/TrialTypes/".$_DATA['trialTypeEditor']['currentTrialTypeName'], 0700); 
      }
      file_put_contents("../Experiments/Common/TrialTypes/".$_DATA['trialTypeEditor']['currentTrialTypeName']."/display.php",$newTrialCode);
      
      ?>
      
      
<script>
  //alert("creating trial type");
</script>