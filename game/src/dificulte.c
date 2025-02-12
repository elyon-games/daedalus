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
            KEY_BLUE, KEY_RED, 
            DOOR_RED_CLOSED
        };
        data->object = easy_objects1;
        return; 
    case 2: // Facile
        data->difficulty = 2;
        data->height = 25;
        data->width = 25;
        data->num_fences = 13;
        data->pour_fencs = 100;

        // Tableau des objets pour la difficulté facile
        static int easy_objects2[] = {
            SHEARS,
            KEY_BLUE, KEY_RED,
            DOOR_RED_CLOSED
        };
        data->object = easy_objects2;
        return;

    case 3: // Facile
        data->difficulty = 3;
        data->height = 30;
        data->width = 30;
        data->num_fences = 15;
        data->pour_fencs = 85;

        // Tableau des objets pour la difficulté facile
        static int easy_objects3[] = {
            SHEARS,LEVER,
            KEY_BLUE, KEY_RED,
            DOOR_RED_CLOSED
        };
        data->object = easy_objects3;
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

