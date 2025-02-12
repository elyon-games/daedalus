#include "Conversion.h"

#include <fcgi_stdio.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "main.h"
#include <time.h>

int main() {

    char* request_method;
    srand(time(NULL));
    int teste;


    int dificult;
    dificulte  difi;
    while (FCGI_Accept() >= 0) {
        printf("Content-Type: text/html\r\n\r\n");
        request_method = getenv("REQUEST_METHOD");
        
        if (request_method != NULL) {


            
            if (strcmp(request_method, "SOLVERA") == 0) {
                dificult = 1; 
                setup_difficulty(&difi, dificult);
                printf(" %d,", difi.width);
                printf("%d,", difi.height);
                do {
                    teste = gestion_solver(&difi);
                } while (teste != 0);

            } 
            else if  (strcmp(request_method, "SOLVERB") == 0) {
                dificult = 2;
                setup_difficulty(&difi, dificult);
                printf(" %d,", difi.width);
                printf("%d,", difi.height);
                do {
                    teste = gestion_solver(&difi);
                } while (teste != 0);

            }
            else if (strcmp(request_method, "SOLVERC") == 0) {
                dificult = 3;
                setup_difficulty(&difi, dificult);
                printf(" %d,", difi.width);
                printf("%d,", difi.height);
                do {
                    teste = gestion_solver(&difi);
                } while (teste != 0);

            }
            else if (strcmp(request_method, "SOLVERD") == 0) {
                dificult = 4;
            }
            else if (strcmp(request_method, "SOLVERE") == 0) {
                dificult = 5;
                setup_difficulty(&difi, dificult);
                printf(" %d,", difi.width);
                printf("%d,", difi.height);
                do {
                    teste = gestion_solver(&difi);
                } while (teste != 0);

            }
            else if (strcmp(request_method, "SOLVERF") == 0) {
                dificult = 6;
                setup_difficulty(&difi, dificult);
                printf(" %d,", difi.width);
                printf("%d,", difi.height);
                do {
                    teste = gestion_solver(&difi);
                } while (teste != 0);

            }
            else if (strcmp(request_method, "SOLVERG") == 0) {
                dificult = 7;
                setup_difficulty(&difi, dificult);
                printf(" %d,", difi.width);
                printf("%d,", difi.height);
                do {
                    teste = gestion_solver(&difi);
                } while (teste != 0);

            }
            else if (strcmp(request_method, "SOLVERH") == 0) {
                dificult = 8;
                setup_difficulty(&difi, dificult);
                printf(" %d,", difi.width);
                printf("%d,", difi.height);
                do {
                    teste = gestion_solver(&difi);
                } while (teste != 0);

            }
            else if (strcmp(request_method, "SOLVERI") == 0) {
                dificult = 9;
                setup_difficulty(&difi, dificult);
                printf(" %d,", difi.width);
                printf("%d,", difi.height);
                do {
                    teste = gestion_solver(&difi);
                } while (teste != 0);

            }
            else if (strcmp(request_method, "SOLVERJ") == 0) {
                dificult = 10;
                setup_difficulty(&difi, dificult);
                printf(" %d,", difi.width);
                printf("%d,", difi.height);
                do {
                    teste = gestion_solver(&difi);
                } while (teste != 0);

            }
                
                
               
              
                

           
        }




        return 0;

    }


} 



gestion_solver(dificulte* data_dificulte) {
    // Initialize the maze (room)
    Room* lym = new_Room(data_dificulte->height, data_dificulte->width);
    if (lym == NULL) {
        fprintf(stderr, "Error: Allocation failed.\n");
        return;
    }

    // Generate maze layout
    generateMaze(lym); // Assuming you have a generateMaze function
    addObjectToMaze(lym, STAIRS_DOWN);// ajoute l'entré du labyrinthe 
    
    



    // Appel de la fonction pour mélanger le tableau
  


    for (int w = 0; w < data_dificulte->num_obt; w++) {
        addObjectToMaze(lym, data_dificulte->object[w]);
    }
    addObjectToMaze(lym, STAIRS_UP);// ajjouter la sortie du labyrinthe 
       
    for (int j = 0; j < data_dificulte->num_fences; j++) {
       
        Objet objetGenere = genererObjet_fence(data_dificulte->pour_fencs);
        addObjectToMaze(lym, objetGenere.id);
    }
   






    int size = sizeof(lym->info) / sizeof(lym->info[0]);
    // Perform searches and validate
    int result;
    for (int k = 1; k < size; k++) {
        result = StarSearch(lym, &lym->info[k-1], &lym->info[k]);
            if (result == 1) {
                switch (lym->info[k].type)
                {
                case SHEARS:
                    lym->Cut = 1;
                case LEVER: 
                    lym->elec = ELECTRIC_OFF; 
                case  KEY_BLUE: 
                    lym->Key_Blue = 1;
                    break; 
                case KEY_GREEN: 
                    lym->Key_Green = 1;
                    break; 
                case KEY_RED: 
                    lym->Key_Red = 1;
                    break; 
                case KEY_YELLOW:
                    lym->Key_Yellow;
                    break; 
                default:
                    break;
                }
                lym->Cut = 1; // Example: Set Cut to 1 upon successful path finding
            }


            else {// on free lym puis en return -1; 
                freeRoom(lym);
                return-1; 
            }
    }

   


    // Print maze layout
    printMaze(lym);

    // Free allocated memory for the maze (room)
    freeRoom(lym);
}




