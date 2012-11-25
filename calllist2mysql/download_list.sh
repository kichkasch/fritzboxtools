#!/bin/sh
#
#inspired by
#http://www.wehavemorefun.de/fritzbox/Anrufliste_von_der_Box_holen

# Challenge abholen
ChallengeXML=`wget -O - "http://192.168.200.1/cgi-bin/webcm?getpage=../html/login_sid.xml" 2>/dev/null| grep Challenge`
Challenge=`echo $ChallengeXML | awk '{match($0,/>[^<>]+</); print substr($0,RSTART+1,RLENGTH-2)}'`

Passwd="*****"
TmpFile="/tmp/fb_anrufliste.csv"

# login aufbauen und hashen
CPSTR="$Challenge-$Passwd"
MD5=`echo -n $CPSTR | iconv -f ISO8859-1 -t UTF-16LE | md5sum -b | awk '{print substr($0,1,32)}'`
RESPONSE="$Challenge-$MD5"
POSTDATA="login:command/response=$RESPONSE&getpage=../html/de/menus/menu2.html"

# login senden und SID herausfischen
SID=`wget -O - --post-data="$POSTDATA" "http://192.168.200.1/cgi-bin/webcm" 2>/dev/null| grep "name=\"sid\"" | head -1 | awk '{match($0,/value="[^"]+"/); print substr($0,RSTART+7,RLENGTH-8)}'`

# refresh der daten auslösen
wget -O /dev/null "http://192.168.200.1//cgi-bin/webcm?sid=$SID&getpage=..%2Fhtml%2Fde%2Fmenus%2Fmenu2.html&errorpage=..%2Fhtml%2Fde%2Fmenus%2Fmenu2.html&var%3Apagename=foncalls&var%3Aerrorpagename=foncalls&var%3Amenu=home&var%3Apagemaster=&var%3Aactivtype=pppoe&var%3AtabInetstat=0&var%3Aweckernr=&logger%3Asettings%2Ffilter=2"

# CSV runterladen
wget -O $TmpFile "http://192.168.200.1/cgi-bin/webcm?sid=$SID&getpage=..%2Fhtml%2Fde%2FFRITZ%21Box_Anrufliste.csv" 2>/dev/null

python updateDatabase.py
rm $TmpFile
