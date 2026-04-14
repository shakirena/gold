<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\assets\AppAsset;
AppAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\Credit */


\yii\web\YiiAsset::register($this);
?>
<div class="container" onafterprint="myFunction()">
    <p align="center">MÖHLƏTLİ SATIŞ MÜQAVİLƏSİ </p> </br>

    <p>

        Zaqatala şəhəri

        &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp	&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp

        <b><?=$credit->date_create?></b> -ci il</br></br></br>

        Bir tərəfdən Azərbaycan Respublikasının qanunvericiliyinə uyğun fəaliyyət göstərən Sulxayev Rafael Aydin oğlu (bundan sonra «Satıcı»),  digər tərəfdən  Azərbaycan Respublikasının vətəndaşı    </br>  </br></br>

       <b><?=$client->name?></b></br>
        (bundan sonra «Alıcı» ), aşağıda göstərilənlər barəsində bu müqaviləni  bağladılar.</br></br></br>

        1. MÜQAVİLƏNİN PREDMETİ</br>

        1.1	Müqaviləyə əsasən «Satıcı» Alıcıya müqaviləyə əlavədə göstərilən mal-material dəyərlilərini satır, «Alıcı» isə həmin malları qəbul edir və razılaşdırılmış müddətlərdə malın haqqını ödəyir.</br></br></br>

        2.	MALLARIN HAQQI VƏ HESABLAŞMA QAYDALARI</br></br></br>

        2.1. Müqavilənin məbləği   <b><?=$credit->sum?> </b> manatdır.</br>
        2.2. Ödəniş «Alıcı» tərəfindən köçürmə və ya nəğd formada möhlətli olaraq həyata keçiriləcəkdir.</br>
        2.3. Müqavilənin bağlanması anına “Alıcı”  <b><?=$credit->fee?></b>   manat məbləğində ilkin ödəniş ödəyir. Qalan məbləği <b> <?=$credit->debt?> </b>  manatı   “Alıcı”   <b><?=$credit->month?> </b> ay müddətində, hər ay növbəti ayın müqavilənin bağlandığı günə müvafiq olan günündə, <b><?=$credit->month_payment?></b> manat olmamaqla ödəyir</br></br></br>

        3.	TƏRƏFLƏRİN MƏSULİYYƏTİ</br></br></br>

        3.1. “Satıcı”</br>
        3.1.1. malın “Alıcı”ya verilməsinədək yaranmış çatışmamazlıqlara görə məsuliyyət daşıyır.</br>
        3.1.2. Mallarla birlikdə onlara aid olan sənədləri verməlidir.</br></br></br>

        3.2. “Alıcı”</br>
        3.2.1. «Alıcı» malları qanuveriçilikdə nəzərdə tutulmuş qaydada qəbul etməlidir.</br>
        3.2.2. Alınmış malların dəyərini müqavilədə nəzərdə tutulmuş müddətdə ödəmədikdə gecikdirilmiş hər gün üçün 5  (beş) manat məbləğində dəbbə pulu ödəyir.</br>
        3.2.3. “Alici” alınmış malların dəyərini müqavilədə nəzərdə tutulmuş müddətdən 1 ay keçəndən  sonra da ödəmədikdə, “Satıcı” satılmış malların geri qaytarılmasını tələb edə bilər. </br>
        3.2.4. Mallar təhvil alınandan və təhvil-təslim sənədləri rəsmiləşdiriləndən sonra «Alıcı» «Satıcıya» malların keyfiyyət və kəmiyyəti barəsində iddia verə bilməz.</br></br></br>

        4.	FORS-MAJOR</br></br></br>

        4.1 Tərəflərin istək və arzularından asılı olmayan, qabaqcadan bilinməsi və ya ağlabatan vasitələrin köməyi ilə qarşısının alınması mümkün olmayan şərait üzündən , o çümlədən, müharibə elan edilməsi , yaxud faktiki müharibə, mülki iğtişaşlar, epidemiyalar, blokada, embarqo, zəlzələlər, daşqınlar, yanğınlar, başqa təbii fəlakət və hökumətin qadağa tədbirləri nəticəsində öz öhdəliklərinin yerinə yetirilməsinə görə tərəflərdən heç biri digər tərəf qarşısında məsuliyyət daşımır.</br></br></br>

        5. MÜBAHİSƏLƏRİN HƏLL EDİLMƏSİ</br></br></br>

        5.1 Bu müqavilə barəsində bütün mübahisələr danışıqlar yolu ilə həll edilir.</br>
        5.2 Razılıq əldə etmək mümkün olmadıqda mübahisələr Azərbaycan Respublikasının qüvvədə olan qanunvericiliyinə müvafiq olaraq məhkəmə qaydasında həll edilir.</br></br></br>

        6. MÜQAVİLƏNİN QÜVVƏDƏ OLMA MÜDDƏTİ</br></br></br>

        6.1 Bu müqavilə imzalandığı gündən alınmış malların dəyərinin tam ödənilməsinədək qüvvədədir.</br>
        6.2 Müqavilə aşağıdakı hallarda ləğv edilə bilər:</br>
        -Tərəflərin razılığına əsasən.</br>
        -Azərbaycan Respublikasının qüvvədə olan qanunvericiliyinə müvafiq qaydada, səlahiyyətli orqanların qərarına əsasən.</br>
        -qarşısıalınmaz qüvvə şəraitinin təsiri nəticəsində .</br>
        6.3 Müqavilənin birtərəfli qaydada ləğv edilməsi yolverilməzdir.</br></br></br>

        7. YEKUN MÜDDƏALAR</br></br></br>

        7.1 Bu müqavilə, tərəflərdən hər biri üçün bir nüsxədən ibarət olmaqla, eyni hüquqi qüvvəyə malik olan iki nüsxədə tərtib edilmişdir.</br>
        7.2 Müqavilədə hər hansı dəyişiklik olunarsa bir tərəf digər tərəfə yazılı məlumat verməlidir.</br></br></br>

        8. TƏRƏFLƏRİN HÜQUQİ ÜNVANLARI VƏ ÖDƏNİŞ REKVİZİTLƏRİ</br></br></br>

    </p>
    <table>
        <tr>
            <td><b>“Satıcı”  </br>
                    Sulxayev Rafael Aydin oğlu</br>
                    VÖEN:  510000000</br>
                    Zaqatala rayonu, Faiq Amirov 15</br>
                    Technotel mağazası</br></br></br>
                    İmza:</br></br></br></br>
                    Möhür  yeri :
                </b></td>

            <td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp   </td>
            <td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp   </td>




            <td><b>“Alıcı”  </br>
                    <?=$client->name?></br>
                    <?=$client->passport?></br>
                    <?=$client->adress?></br>
					<?=$client->phone?></br></br></br>
                    İmza:</br></br></br></br>
                   
                </b></td>

        </tr>



    </table>





</div>
<?php
$script = <<< JS

$(document).ready(function () {
window.print();

});

JS;
$this->registerJs($script);