package com.example;

import java.util.List;

public class Abonne {
    private String id;
    private String prenom;
    private TrancheAge ageEnfant;
    private List<Categorie> preferences; 

    public Abonne(String id, String prenom, TrancheAge ageEnfant, List<Categorie> preferences) {
        this.id = id;
        this.prenom = prenom;
        this.ageEnfant = ageEnfant;
        this.preferences = preferences;
    }

    public String getPrenom() { return prenom; }
    public TrancheAge getAgeEnfant() { return ageEnfant; }
    public List<Categorie> getPreferences() { return preferences; }
}