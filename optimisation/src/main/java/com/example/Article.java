package com.example;

public class Article {
    private String id;
    private String designation;
    private Categorie categorie;
    private TrancheAge age;
    private Etat etat;
    private int prix;
    private int poids;

    public Article(String id, String designation, Categorie categorie, TrancheAge age, Etat etat, int prix, int poids) {
        this.id = id;
        this.designation = designation;
        this.categorie = categorie;
        this.age = age;
        this.etat = etat;
        this.prix = prix;
        this.poids = poids;
    }

    public String getId() { return id; }
    public Categorie getCategorie() { return categorie; }
    public TrancheAge getAge() { return age; }
    public Etat getEtat() { return etat; }
    public int getPoids() { return poids; }
}