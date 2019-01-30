<!doctype html>
<html>
<head>
    <title>Бланк "Счет на оплату"</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body { width: 210mm;height:170mm; margin-left: auto; margin-right: auto; border: 1px #efefef solid; font-size: 11pt;}
		table.invoice_bank_rekv { border-collapse: collapse; border: 1px solid; }
		table.invoice_bank_rekv > tbody > tr > td, table.invoice_bank_rekv > tr > td { border: 1px solid; }
	    table.invoice_items { border: 1px solid; border-collapse: collapse;}
        table.invoice_items td, table.invoice_items th { border: 1px solid;}
    </style>
</head>
<body>

@php
$localtop=0;
if($inv->type==2)
$localtop=24;	
@endphp
<div style="width: 210mm; padding:2mm;">
Внимание! Счет действителен до {{Carbon\Carbon::parse( $inv->datetime)->format('d.m.Y')}}. Оплата данного счета означает согласие с условиями поставки товара. 
Уведомление об оплате обязательно, в противном случае не гарантируется наличие товара на складе. 
Товар отпускается по факту прихода денег на р/с Поставщика, самовывозом, при наличии доверенности и паспорта.
</div>
{{--
@if($inv->type==2)
<div style="width: 210mm;">
Условия для расчетов:
<ol>
<li>Отсрочка платежа. Оплата 100% суммы по счету должна быть произведена в течении 14 календарных дней с момента выставления счета.
</li>
<li>В назначении платежа, пожалуйста, указывайте номер счета.
</li>
</ol>
</div>
@endif
--}}
<div style="position:relative; width: 210mm;height:170mm;">
<div style="position:absolute;top:0mm;width:100%;height:35mm;">
<table width="100%" cellpadding="2" cellspacing="2" class="invoice_bank_rekv">
    <tr>
        <td colspan="2" rowspan="2" style="min-height:13mm; width: 105mm;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="height: 13mm;">
                <tr>
                    <td valign="top">
                        <div>{{$postavka["bank_name"]}}</div>
                    </td>
                </tr>
                <tr>
                    <td valign="bottom" style="height: 3mm;">
                        <div style="font-size:10pt;">Банк получателя        </div>
                    </td>
                </tr>
            </table>
        </td>
        <td style="min-height:7mm;height:auto; width: 25mm;">
            <div>БИK</div>
        </td>
        <td rowspan="2" style="vertical-align: top; width: 60mm;">
            <div style=" height: 7mm; line-height: 7mm; vertical-align: middle;">{{$postavka["bik"]}}</div>
            <div>{{$postavka["rs"]}}</div>
        </td>
    </tr>
    <tr>
        <td style="width: 25mm;">
            <div>Р/С</div>
        </td>
    </tr>	
    <tr>
        <td style="min-height:6mm; height:auto; width: 50mm;">
            <div>ИНН {{$postavka["inn"]}}</div>
        </td>
        <td style="min-height:6mm; height:auto; width: 55mm;">
            <div>КПП {{$postavka["kpp"]}}</div>
        </td>
        <td rowspan="2" style="min-height:19mm; height:auto; vertical-align: top; width: 25mm;">
            <div>К/С</div>
        </td>
        <td rowspan="2" style="min-height:19mm; height:auto; vertical-align: top; width: 60mm;">
            <div>{{$postavka["ks"]}}</div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="min-height:13mm; height:auto;">

            <table border="0" cellpadding="0" cellspacing="0" style="height: 13mm; width: 105mm;">
                <tr>
                    <td valign="top">
                        <div>
                        {{$postavka["firm_name"]}}</div>
                    </td>
                </tr>
                <tr>
                    <td valign="bottom" style="height: 3mm;">
                        <div style="font-size: 10pt;">Получатель</div>
                    </td>
                </tr>
            </table>

        </td>
    </tr>	
</table>
</div>
@if($inv->type==2)
<div style="position:absolute;top:38mm;width:210mm;height:{{$localtop}}mm; font-size: 10pt;">	
<div style ="text-decoration:underline">Условия для расчётов:</div>
<div style ="font-size: 12pt;padding-left:2mm">
<ol style="margin: 0;">
<li>Отсрочка платежа. Оплата 100% суммы по счету должа быть произведена в течении 14 календарых дней с момента выставления счёта</li>
<li>В назначении платежа, пожалуйста, указывайте номер счёта.</li>
</ol>
</div>

</div>
@endif	
<div style="position:absolute;top:{{(38+$localtop)}}mm;width:208mm;height:6mm;font-weight: bold; font-size: 16pt;left:2mm">
    Счет № {{$inv->number}} от {{Carbon\Carbon::parse( $inv->datetime)->format('d.m.Y')}}</div>
<div style="position:absolute;top:{{(48+$localtop)}}mm;background-color:#000000; width:100%; font-size:1px; height:2px;">&nbsp;</div>
<div style="position:absolute;top:{{(49+$localtop)}}mm;width:100%;height:20mm;">
<table width="100%">
    <tr>
        <td style="vertical-align:top; width: 20mm;height:5mm">
            <div style="padding-left:2px;">Поставщик:    </div>
        </td>
        <td style="vertical-align:top;">
            <div style="position:relative;height:9mm;font-weight:bold; padding-left:2px;">
			<div style="position:absolute;width:100%;height:4.5mm;"></div>
                {{$postavka["firm_name"]}}, ИНН {{$postavka["inn"]}} @if($postavka["kpp"]),КПП :{{$postavka["kpp"]}}  @endif,{{$postavka["legale_mail"]}}          
		   </div>
        </td>
    </tr>
	 <tr>
        <td style="vertical-align:top; width: 20mm;height:5mm">
            <div style="padding-left:2px;">Покупатель:    </div>
        </td>
        <td style="vertical-align:top;">
            <div style="position:relative;height:9mm;font-weight:bold; padding-left:2px;">
			<div style="position:absolute;width:100%;height:4.5mm;"></div>
                @if($inv->Requisite->type_org==1)ИП@elseООО@endif {{$inv->Requisite->firm_name}}, ИНН :{{$inv->Requisite->inn}}@if($inv->Requisite->kpp), КПП :{{$inv->Requisite->kpp}}@endif, {{$inv->Requisite->legale_male}}           
		   </div>
        </td>
    </tr>
</table>	
</div>
<div style="position:absolute;top:{{(70+$localtop)}}mm;width:100%;">
<table class="invoice_items" width="100%" cellpadding="2" cellspacing="2">
    <thead>
    <tr>
	
        <th style="width:9mm;">№</th>
        <th>Товар</th>
        <th style="width:17mm;">Кол-во</th>
        <th style="width:17mm;">Ед.</th>
        <th style="width:27mm;">Цена</th>
		@if($inv->Requisite->type_payout==2)
		<th style="width:20mm;white-space:nowrap">Ставка НДС</th>
        <th style="width:20mm;white-space:nowrap">Сумма НДС</th>
		@endif	
        <th style="width:27mm;">Сумма</th>
    </tr>
    </thead>
    <tbody>
	    <tr>
                <td>1</td>

        <td style="font-size: 10px">Услуги по размещению информационных материалов заказчика в рекламной сети market-place.su согласно договору
            оферты, логин ЛОГИН
        </td>
        <td>1</td>
        <td>-</td>
        <td>{{$inv->summa}}</td>
		@if($inv->Requisite->type_payout==2)
			 <td>18%</td>
		     <td>{{$inv->nds}}</td>
		@endif	
        <td>{{$inv->summa}}</td>
    </tr>
    </tr>
	</tbody>
</table>
</div>

<div style="position:absolute;top:{{(87+$localtop)}}mm;width:100%;height:16mm;">
<table width="100%" cellpadding="1" cellspacing="1" border=0>
   <tr>
        <td></td>
        <td style="width:27mm; font-weight:bold;  text-align:right;">Итого:</td>
        <td style="width:27mm; font-weight:bold;  text-align:right;">{{$inv->summa}} руб.</td>
    </tr>
	<tr>
        <td></td>
		@if($inv->Requisite->type_payout==2)
		<td style="width:27mm; font-weight:bold;  text-align:right;">НДС (%18)</td>
        <td style="width:27mm; font-weight:bold;  text-align:right;">{{$inv->nds}}</td>
		@else
        <td style="width:27mm; font-weight:bold;  text-align:right;">Без НДС</td>
        <td style="width:27mm; font-weight:bold;  text-align:right;"></td>
		@endif
    </tr>
	    <tr>
        <td></td>
        <td style="width:27mm; font-weight:bold;  text-align:right;">Всего к оплате:</td>
        <td style="width:27mm; font-weight:bold;  text-align:right;">{{$inv->total}} руб.</td>
    </tr>
</table>
</div>
<div style="position:absolute;top:{{(101+$localtop)}}mm;">
  Всего наименований 1 на сумму {{$inv->total}} рублей.<br />
    <span style="font-weight: 600;">{{$inv->summaToStr()}}</span></div>
<div style="position:absolute;top:{{(115+$localtop)}}mm;background-color:#000000; width:100%; font-size:1px; height:2px;">&nbsp;</div>
<div style="position:absolute;top:{{(121+$localtop)}}mm;">Руководитель <span style="border-bottom: 1px solid #000; width:310px;display: inline-block;text-align: right;" >Осипов Д.А.</span></div>    
<div style="position:absolute;top:{{(132+$localtop)}}mm;">Главный бухгалтер <span style="border-bottom: 1px solid #000; width: 310px;display: inline-block;text-align: right;" >Осипов Д.А.</span> </div>
@if($inv->type==2)
<div style="position:absolute;top:{{(143+$localtop)}}mm;">Счет действителен к оплате в течении 14 календарных дней.</div>
@else	
<div style="position:absolute;top:{{(143+$localtop)}}mm;">Счет действителен к оплате в течении трех дней.</div>
@endif
 <img style="width: 190px;
    position: absolute;
    top: {{(110+$localtop)}}mm;
    left: 79px;  
" src="https://storage.market-place.su/invoice/pec.png" alt="" id="pechat"/>
<img style="width: 130px;
    position: absolute;
    top: {{(111+$localtop)}}mm;
    left: 240px;
" src="https://storage.market-place.su/invoice/pod.png" alt="" id="pod"/>
<img style="width: 130px;
    position: absolute;
    top: {{(122+$localtop)}}mm;
    left: 260px;
" src="https://storage.market-place.su/invoice/pod.png" alt="" id="pod2"/>

   </div>
</body>
</html>
<script>
if(typeof window.MpBridgeListenerAttached=="undefined"){
    if (window.addEventListener) {
        window.addEventListener("message",mylistener);
    } else {
        // IE8
        window.attachEvent("onmessage",  mylistener);
    }
   
}
function mylistener(event){
	if(event.data=='print'){
		window.print();
	}
	
}

</script>
