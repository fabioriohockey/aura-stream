import { useParams, Link } from "react-router-dom";
import Navbar from "@/components/Navbar";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Play, Plus, Share2, Star } from "lucide-react";
import heroDrama from "@/assets/hero-drama.jpg";
import drama1 from "@/assets/drama-1.jpg";

const DoramaDetails = () => {
  const { id } = useParams();

  // Mock data - in real app, fetch based on id
  const dorama = {
    id: "1",
    title: "Destined Hearts",
    poster: drama1,
    backdrop: heroDrama,
    rating: 9.2,
    year: 2024,
    episodes: 16,
    status: "Ongoing",
    genres: ["Romance", "Drama", "Fantasy"],
    synopsis:
      "A heartwarming tale of fate, love, and second chances set against the vibrant backdrop of Seoul. When two souls from different worlds collide, their journey becomes an unforgettable story of romance and destiny. As they navigate through life's challenges, they discover that some connections are written in the stars.",
    cast: [
      { name: "Park Ji-won", role: "Lee Soo-jin", image: drama1 },
      { name: "Kim Min-ho", role: "Kang Ji-hoo", image: drama1 },
      { name: "Lee Yeon-hee", role: "Choi Mi-ra", image: drama1 },
    ],
    episodes_list: [
      { number: 1, title: "Fated Encounter", duration: "65 min" },
      { number: 2, title: "Crossing Paths", duration: "60 min" },
      { number: 3, title: "Hidden Feelings", duration: "62 min" },
      { number: 4, title: "Confession", duration: "58 min" },
      { number: 5, title: "Distance", duration: "63 min" },
    ],
  };

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      
      <div className="pt-16">
        {/* Backdrop Hero */}
        <section className="relative h-[60vh] w-full overflow-hidden">
          <div className="absolute inset-0">
            <img
              src={dorama.backdrop}
              alt={dorama.title}
              className="w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-r from-background via-background/60 to-transparent" />
            <div className="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent" />
          </div>
        </section>

        {/* Content */}
        <div className="container mx-auto px-6 -mt-40 relative z-10">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {/* Poster */}
            <div className="md:col-span-1">
              <Card className="overflow-hidden border-none shadow-xl">
                <img
                  src={dorama.poster}
                  alt={dorama.title}
                  className="w-full h-auto"
                />
              </Card>
            </div>

            {/* Info */}
            <div className="md:col-span-2 space-y-6">
              <div>
                <h1 className="text-4xl md:text-5xl font-bold text-foreground mb-4">
                  {dorama.title}
                </h1>
                
                <div className="flex flex-wrap items-center gap-4 mb-6">
                  <div className="flex items-center space-x-2 text-foreground">
                    <Star className="h-5 w-5 fill-primary text-primary" />
                    <span className="text-xl font-semibold">{dorama.rating}</span>
                  </div>
                  <Badge variant="outline" className="text-sm border-primary text-primary">
                    {dorama.year}
                  </Badge>
                  <Badge variant="outline" className="text-sm">
                    {dorama.episodes} Episodes
                  </Badge>
                  <Badge variant="outline" className="text-sm border-primary text-primary">
                    {dorama.status}
                  </Badge>
                </div>

                <div className="flex flex-wrap gap-2 mb-6">
                  {dorama.genres.map((genre) => (
                    <Badge key={genre} className="bg-secondary text-secondary-foreground">
                      {genre}
                    </Badge>
                  ))}
                </div>

                <div className="flex flex-wrap gap-3">
                  <Link to={`/watch/${dorama.id}`}>
                    <Button size="lg" className="bg-primary hover:bg-primary-light text-primary-foreground px-8">
                      <Play className="h-5 w-5 mr-2 fill-current" />
                      Watch Now
                    </Button>
                  </Link>
                  <Button size="lg" variant="outline" className="border-foreground/20">
                    <Plus className="h-5 w-5 mr-2" />
                    Add to List
                  </Button>
                  <Button size="lg" variant="outline" className="border-foreground/20">
                    <Share2 className="h-5 w-5 mr-2" />
                    Share
                  </Button>
                </div>
              </div>

              {/* Synopsis */}
              <div className="space-y-3">
                <h2 className="text-2xl font-bold text-foreground">Synopsis</h2>
                <p className="text-foreground/80 leading-relaxed">{dorama.synopsis}</p>
              </div>

              {/* Cast */}
              <div className="space-y-4">
                <h2 className="text-2xl font-bold text-foreground">Cast</h2>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-4">
                  {dorama.cast.map((actor, index) => (
                    <Card key={index} className="overflow-hidden border-none shadow-md">
                      <CardContent className="p-0">
                        <img
                          src={actor.image}
                          alt={actor.name}
                          className="w-full h-48 object-cover"
                        />
                        <div className="p-3">
                          <p className="font-semibold text-foreground">{actor.name}</p>
                          <p className="text-sm text-muted-foreground">{actor.role}</p>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              </div>

              {/* Episodes */}
              <div className="space-y-4">
                <h2 className="text-2xl font-bold text-foreground">Episodes</h2>
                <div className="space-y-2">
                  {dorama.episodes_list.map((episode) => (
                    <Link key={episode.number} to={`/watch/${dorama.id}?episode=${episode.number}`}>
                      <Card className="hover:bg-accent transition-colors cursor-pointer border-border">
                        <CardContent className="p-4 flex items-center justify-between">
                          <div className="flex items-center space-x-4">
                            <div className="bg-primary/10 text-primary font-semibold rounded-lg px-3 py-2">
                              {episode.number}
                            </div>
                            <div>
                              <p className="font-medium text-foreground">{episode.title}</p>
                              <p className="text-sm text-muted-foreground">{episode.duration}</p>
                            </div>
                          </div>
                          <Play className="h-5 w-5 text-primary" />
                        </CardContent>
                      </Card>
                    </Link>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="h-20" />
      </div>
    </div>
  );
};

export default DoramaDetails;
