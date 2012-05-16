#Google Favicon Helper
```
//Create a new object of the service wrapper
$Favicon = new Google_FavIcon_Service();

//This function takes a parameter of the url, to get the fav icon of
$Favicon->Favicon("http://illution.dk");
```

#goo.gl API Wrapper

#Gravatar API Wrapper

#Google Calculator API Wrapper

#Google Closure Compiler Helper

#Google Page Speed API Wrapper
To use this api you need to have a Google API key,
in this wrapper it can be deffined in the constructor or
using the Api_Key() function

```
$PageSpeed = new GooglePageSpeed("YOUR_API_KEY");

//This function returns FALSE if it fails or a std Object if success containing the data
//The only parameter it takes is the url to analyze
$PageSpeed->PageSpeed("http://illution.dk");

```

#Masterbranch API Wrapper

#Open Exchange Rates API Wrapper

#Placehold.it Helper

#PasteHTML API Wrapper

#Prefixr API Wrapper

#shr.im API Wrapper

#Xbox Live API Wrapper