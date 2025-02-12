#include "Conversion.h"
//#include <fcgi_stdio.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

char* Conversion_HTTP() { // permet de récupere les donne d'une requette HTTP
    char* request_method = getenv("REQUEST_METHOD"); // récupére le type de méthode utiliser 
    char* content_type = getenv("CONTENT_TYPE");// récupré le type du contenue de la requette HTTP
    int content_length = atoi(getenv("CONTENT_LENGTH") ? getenv("CONTENT_LENGTH") : "0");


    if (content_length > 0) {
        char* content = (char*)malloc(sizeof content_length + 1);

        if (content != NULL) {
            fread(content, content_length, 1, stdin);
            content[content_length] = '\0';
            return(content);


        }

    }
    return(NULL);
}


void affichier(char *data){
        printf("Content-Type: text/html\r\n\r\n");
        printf("Requestdd Message tetet %s", data);
        return;
}