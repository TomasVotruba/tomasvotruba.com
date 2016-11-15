---
id: 3
title: "Jak si levně udělat živý web, i když nejsi ajťák"
perex: "Znáš pojmy jako hosting, doména, HTML, CSS, FTP, šablona, Wordpress nebo Github? Máš vyhráno, dál nečti a běž se podívat na svou krásnou online vizitku."
thumbnail: "github.jpg"
---

<p class="perex">{$perex|noescape}</p>

Na [MyEagers konferenci](http://myeagers.beeager.com/) jsem se stal svědkem workshopu [Lekce svádění aneb jak to udělat online tak, aby Tě chtěli](https://www.facebook.com/events/885079758208224/permalink/896505960398937/). **Kopa šikovných hacků, které ti dají světelný náskok na tvé pracovní cestě**.

Cílem bylo ukázat, že si i web jako [jakserodicopywriter.cz](http://jakserodicopywriter.cz) můžeš udělat za víkend a 150 Kč.

Chceš si taky udělat podobný web, ale tvorba webových stránek je ti zatím cizí? Ukážeme si, jak to zvládnout levou zadní.

Tohle využiješ, pokud...

### ...chceš vytvořit

- webové stránky na vlastní doméně
- nabídnout lidem své služby *nebo*
- získat konkrétní pracovní pozici


### Na co se spolu podíváme

- jak si koupit doménu
- jak ji propojit s webem online 
- jak si stránky upravit jen pomocí prohlížeče


## Jak na to ve 3 krocích


### 1. Moje první doména

1. Vymysli si doménu - to je to, co člověk zadá do adresního řádku (např. www.tadybudetvujtext.cz)
2. Zjisti, [jestli je doména ještě volná](https://hosting.wedos.com/cs/domain-check.html?affd=79496)
3. Kup si ji - my jsme v tomto případě použili český hosting [Wedos.cz](https://hosting.wedos.com/cs/domain-check.html?affd=79496)
    - aktuálně stojí 151 Kč, to bude veškerá tvá investice
    - u možnosti webhostingu dej "neobjednávat"

### 2. První web - Github

Stránky můžeš mít umístěné na Github zdarma. Github je sociální síť nejen pro ajťáky, kde můžeš sdílet zdrojový kód.

1. registruj se na [Githubu](https://github.com/)
2. "forkni" si mé [ukázkové stránky](https://github.com/TomasVotruba/php7.cz) - tím si **základ pro vlastní stránku překopíruješ k sobě** a můžeš si ji dál upravovat, jak budeš chtít

    <div class="text-center">
        <img src="/../../../../images/posts/2015-11-16/github-1.png" alt="Forkneme = stáhneme k sobě">
        <br>
        <em>Klikni na tlačítko "fork"</em>
    </div>
    <br>
3. mé stránky už teď najdeš na adrese [tomasvotruba.github.io/php7.cz](http://tomasvotruba.github.io/php7.cz)
4. ty své pak na `http://<tve-jmeno>.github.io/<tva-domena>.cz`
 

### 3. Propojení domény a webu

Teď už máme doménu a stránky online, ale zatím jsou od sebe vzájemně oddělené. Teď nás čeká jejich propojení.


### Na Githubu

1. otevři si nastavení svého webu

    <div class="text-center">
        <img src="/../../../../images/posts/2015-11-16/github-2.png" alt="Otevři 'Settings'">
        <br>
        <em>Otevři "Settings"</em>
    </div>
    <br>
2. přejmenuj složku na název tvé nové domény (v mém případě `php7.cz`)

    <div class="text-center">
        <img src="/../../../../images/posts/2015-11-16/github-3.png" alt="Napiš svou doménu">
        <br>
        <em>Napiš svou doménu</em>
    </div>
    <br>
3. ve složce uprav `CNAME` soubor 

    <div class="text-center">
        <img src="/../../../../images/posts/2015-11-16/github-4.png" alt="Otevři soubor CNAME">
        <br>
        <em>Otevři CNAME soubor</em>
    </div>
    <br>
    <div class="text-center">
        <img src="/../../../../images/posts/2015-11-16/github-5.png" alt="Dej editovat">
        <br>
        <em>Dej editovat</em>
    </div>
    <br>
4. do prvního řádku napiš název své domény bez `http://www.`, tedy např. `php7.cz` 
5. dole klikni na "Commit changes", tedy "Uložit"

Teď už ti zbývá poslední krok. 

### Na Wedosu 

1. přihlaš se na Wedos a otevři si [seznam tvých domén](https://client.wedos.com/domain) 
2. klikni na svou doménu
3. dej "editovat DNS záznamy"

    <div class="text-center">
        <img src="/../../../../images/posts/2015-11-16/domain-dns-1.png" alt="Edituj DNS záznamy">
        <br>
        <em>Edituj DNS záznamy</em>
    </div>
    <br>
4. změň A záznam a smaž AAAA záznamy

    <div class="text-center">
        <img src="/../../../../images/posts/2015-11-16/domain-dns-2.png" alt="Edituj DNS záznamy" style="max-width:270px">
        <br>
        <em>Uprav oba záznamy - jeden s hvězdičkou a druhý bez</em>
    </div>
    <br>
5. IP adresu změň na `192.30.252.153` - to je adresa Githubu, tam kde tvé stránky teď sídlí

    <div class="text-center">
        <img src="/../../../../images/posts/2015-11-16/domain-dns-3.png" alt="Změň IP adresu">
        <br>
        <em>Změň IP adresu</em>
    </div>
    <br>
6. klikni na tlačítko "Aplikovat změny"
    
    <div class="text-center">
        <img src="/../../../../images/posts/2015-11-16/domain-dns-4.png" alt="Aplikuj změny">
        <br>
        <em>Aplikuj změny</em>
    </div>
    <br>

Tato operace může trvat pár desítek minut, tak si zatím dej kafe.

Pak bude tvá doména směřovat na tvé stránky na Githubu. 

Když otevřeš svou doménu (v mém případě [php7.cz](http://php7.cz)), uvidíš své stránky v plné kráse.


A máme hotovo!

Příště si ukážeme, jak si stránky vymazlit, představíme si základní HTML tagy (**tučné písmo**, *kurzíva*...) a naučíme se, jak přidat obrázky.
