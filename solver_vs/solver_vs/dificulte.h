#pragma once


typedef struct dificulte {
	int difficulty; 
	int width; 
	int height; 
	int num_fences;
	int pour_fencs; 
	int* object; 
	int num_obt;
}dificulte; 


// Structure d'un objet
typedef struct {
	int id;
	int pourcentage;
} Objet;


Objet genererObjet_fence(int dificulte);

void setup_difficulty(dificulte* data, int dificulte);

void shuffleArray(int* array, int size); 