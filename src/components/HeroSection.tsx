import { Play, Plus, Info } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Link } from "react-router-dom";

interface HeroSectionProps {
  id: string;
  title: string;
  description: string;
  backdrop: string;
  rating: number;
  year: number;
  genres: string[];
}

const HeroSection = ({ id, title, description, backdrop, rating, year, genres }: HeroSectionProps) => {
  return (
    <section className="relative h-[85vh] w-full overflow-hidden">
      {/* Background Image */}
      <div className="absolute inset-0">
        <img
          src={backdrop}
          alt={title}
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-background via-background/80 to-transparent" />
        <div className="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent" />
      </div>

      {/* Content */}
      <div className="relative container mx-auto px-6 h-full flex items-center">
        <div className="max-w-2xl space-y-6">
          {/* Title */}
          <h1 className="text-5xl md:text-7xl font-bold text-foreground leading-tight">
            {title}
          </h1>

          {/* Meta */}
          <div className="flex items-center space-x-4 text-foreground/90">
            <div className="flex items-center space-x-1">
              <span className="text-primary font-semibold">★</span>
              <span className="font-medium">{rating.toFixed(1)}</span>
            </div>
            <span>•</span>
            <span>{year}</span>
            <span>•</span>
            <span>{genres.join(", ")}</span>
          </div>

          {/* Description */}
          <p className="text-lg text-foreground/80 leading-relaxed line-clamp-3">
            {description}
          </p>

          {/* Actions */}
          <div className="flex items-center space-x-4 pt-2">
            <Link to={`/watch/${id}`}>
              <Button size="lg" className="bg-primary hover:bg-primary-light text-primary-foreground px-8 shadow-lg">
                <Play className="h-5 w-5 mr-2 fill-current" />
                Assistir Agora
              </Button>
            </Link>
            <Button size="lg" variant="outline" className="border-foreground/20 hover:border-primary hover:text-primary">
              <Plus className="h-5 w-5 mr-2" />
              Minha Lista
            </Button>
            <Link to={`/dorama/${id}`}>
              <Button size="lg" variant="ghost" className="hover:bg-accent hover:text-primary">
                <Info className="h-5 w-5 mr-2" />
                Mais Informações
              </Button>
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
};

export default HeroSection;
