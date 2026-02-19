package com.example;

import java.util.ArrayList;
import java.util.List;

public class Box {
    private Abonne abonne;
    private List<Article> articles;
    private int poidsTotal;

    public Box(Abonne abonne) {
        this.abonne = abonne;
        this.articles = new ArrayList<>();
        this.poidsTotal = 0;
    }

    public boolean ajouterArticle(Article article, int maxPoids) {
        if (article.getAge() == abonne.getAgeEnfant() && (poidsTotal + article.getPoids() <= maxPoids)) {
            articles.add(article);
            poidsTotal += article.getPoids();
            return true;
        }
        return false;
    }

    public List<Article> getArticles() { return articles; }
    public Abonne getAbonne() { return abonne; }
    public int getPoidsTotal() { return poidsTotal; }
}