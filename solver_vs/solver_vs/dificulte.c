#include "main.h"
void setup_difficulty(dificulte* data, int dificulte) {
    switch (dificulte) {
    case 1: // Facile
        data->difficulty = 1;
        data->height = 20;
        data->width = 20;
        data->num_fences = 10;
        data->pour_fencs = 100;

        // Tableau des objets pour la difficulté facile
        static int easy_objects1[] = {
            SHEARS,
             KEY_RED, 
            DOOR_RED_CLOSED
        };
        data->object = easy_objects1;
        data->num_obt = 3; 
        return; 
    case 2: // Facile
        data->difficulty = 2;
        data->height = 25;
        data->width = 25;
        data->num_fences = 13;
        data->pour_fencs = 100;

        // Tableau des objets pour la difficulté facile
        static int easy_objects2[] = {
            SHEARS,LEVER,
             KEY_RED,LEVER,
            DOOR_RED_CLOSED
        };
        data->object = easy_objects2;
        data->num_obt = 5;
        return;

    case 3: // Facile
        data->difficulty = 3;
        data->height = 30;
        data->width = 30;
        data->num_fences = 15;
        data->pour_fencs = 85;

        // Tableau des objets pour la difficulté facile
        static int easy_objects3[] = {
           SHEARS,LEVER,SHEARS,LEVER,
            KEY_BLUE, KEY_RED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED
        };
        data->object = easy_objects3;
        data->num_obt = 8;
        return;

    case 4: // moyen
        data->difficulty = 4;
        data->height = 35;
        data->width = 35;
        data->num_fences = 20;
        data->pour_fencs = 75;

        // Tableau des objets pour la difficulté facile
        static int easy_objects4[] = {
            SHEARS,LEVER,SHEARS,LEVER,
            KEY_BLUE, KEY_RED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED
        };
        data->object = easy_objects4;
        data->num_obt = 10;
        return;

    case 5: // moyen
        data->difficulty = 5;
        data->height = 35;
        data->width = 35;
        data->num_fences = 20;
        data->pour_fencs = 75;

        // Tableau des objets pour la difficulté facile
        static int easy_objects5[] = {
            SHEARS,LEVER,SHEARS,LEVER,
            KEY_BLUE, KEY_RED,KEY_GREEN,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_GREEN_CLOSED
        };
        data->object = easy_objects5;
        data->num_obt = 12;
        return;

    case 6: // moyen
        data->difficulty = 6;
        data->height = 40;
        data->width = 40;
        data->num_fences = 30;
        data->pour_fencs = 70;

        // Tableau des objets pour la difficulté facile
        static int easy_objects6[] = {
            SHEARS,LEVER,SHEARS,LEVER,
            KEY_BLUE, KEY_RED,KEY_GREEN,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_GREEN_CLOSED

        };
        data->object = easy_objects6;
        data->num_obt = 13;
        return;

    case 7: // moyen
        data->difficulty = 7;
        data->height = 45;
        data->width = 45;
        data->num_fences = 35;
        data->pour_fencs = 65;

        // Tableau des objets pour la difficulté facile
        static int easy_objects7[] = {
            SHEARS,LEVER,SHEARS,LEVER,
            KEY_BLUE, KEY_RED,KEY_GREEN,
            DOOR_RED_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_GREEN_CLOSED
        };
        data->object = easy_objects7;
        data->num_obt = 14;
        return;

    case 8: // difficile
        data->difficulty = 8;
        data->height = 50;
        data->width = 50;
        data->num_fences = 40;
        data->pour_fencs = 65;

        // Tableau des objets pour la difficulté facile
        static int easy_objects8[] = {
             SHEARS,LEVER,SHEARS,LEVER,
            KEY_BLUE, KEY_RED,KEY_GREEN, KEY_YELLOW,
            DOOR_RED_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_YELLOW_CLOSED
        };
        data->object = easy_objects8;
        data->num_obt = 16;
        return;

    case 9: // difficile
        data->difficulty = 9;
        data->height = 55;
        data->width = 55;
        data->num_fences = 45;
        data->pour_fencs = 65;

        // Tableau des objets pour la difficulté facile
        static int easy_objects9[] = {
            SHEARS,LEVER,SHEARS,LEVER,
            KEY_BLUE, KEY_RED,KEY_GREEN, KEY_YELLOW,
            DOOR_RED_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_YELLOW_CLOSED,
            DOOR_YELLOW_CLOSED
        };
        data->object = easy_objects9;
        data->num_obt = 18;
        return;

    case 10: // difficile
        data->difficulty = 10;
        data->height = 60;
        data->width = 60;
        data->num_fences = 50;
        data->pour_fencs = 65;

        // Tableau des objets pour la difficulté facile
        static int easy_objects10[] = {
            SHEARS,LEVER,SHEARS,LEVER,
            KEY_BLUE, KEY_RED,KEY_GREEN, KEY_YELLOW,
            DOOR_RED_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_RED_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_YELLOW_CLOSED,
            DOOR_YELLOW_CLOSED,
            DOOR_BLUE_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_GREEN_CLOSED,
            DOOR_YELLOW_CLOSED,
            DOOR_YELLOW_CLOSED
        };
        data->object = easy_objects10;
        data->num_obt = 23;
        return;
   
    default:

        data->difficulty = 1;
        data->height = 20;
        data->width = 20;
        data->num_fences = 10;
        data->pour_fencs = 100;

        // Tableau des objets pour la difficulté facile
        static int easy_objects0[] = {
            SHEARS,
            KEY_BLUE, KEY_RED,
            DOOR_RED_CLOSED
        };
        data->object = easy_objects0;
        return;
      

        return;

    }
}


void shuffleArray(int* array, int size) {
    if (size > 1) {
        srand(time(NULL));  // Initialisation du générateur de nombres aléatoires

        for (int i = size - 1; i > 0; i--) {
            // Générer un index aléatoire entre 0 et i inclusivement
            int j = rand() % (i + 1);

            // Échanger l'élément actuel avec l'élément à l'index aléatoire
            int temp = array[i];
            array[i] = array[j];
            array[j] = temp;
        }
    }
}



// Fonction pour générer un objet en fonction des pourcentages d'apparition
Objet genererObjet_fence(int dificulte) {
    Objet objects[] = {

        { FENCE_ELECTRIFIED,  dificulte },
        { FENCE,  (100- dificulte) }


    };

    // Générer un nombre aléatoire entre 1 et 100
    int randomNum = rand() % 100 + 1;
    int totalPourcentage = 0;

    // Parcourir les objets et déterminer celui qui doit apparaître
    for (int i = 0; i < sizeof(objects) / sizeof(objects[0]); i++) {
        totalPourcentage += objects[i].pourcentage;
        if (randomNum <= totalPourcentage) {
            return objects[i]; // Retourner l'objet correspondant
        }
    }

    // Par défaut, retourner le dernier objet du tableau en cas d'erreur ou de dépassement
    return objects[sizeof(&objects) / sizeof(objects[0]) - 1];
}

