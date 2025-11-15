import { Link } from "react-router-dom";
import { Star, Play } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";

interface DoramaCardProps {
  id: string;
  title: string;
  poster: string;
  rating: number;
  year: number;
  episodes?: number;
}

const DoramaCard = ({ id, title, poster, rating, year, episodes }: DoramaCardProps) => {
  return (
    <Link to={`/dorama/${id}`}>
      <Card className="group overflow-hidden border-none shadow-md hover:shadow-xl transition-all duration-300 bg-card">
        <CardContent className="p-0 relative">
          {/* Poster */}
          <div className="relative aspect-[2/3] overflow-hidden bg-muted">
            <img
              src={poster}
              alt={title}
              className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
            
            {/* Hover Overlay */}
            <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
              <Button size="sm" className="bg-primary hover:bg-primary-light text-primary-foreground">
                <Play className="h-4 w-4 mr-1" />
                Watch Now
              </Button>
            </div>
          </div>

          {/* Info */}
          <div className="p-4 space-y-2">
            <h3 className="font-semibold text-foreground line-clamp-1 group-hover:text-primary transition-colors">
              {title}
            </h3>
            <div className="flex items-center justify-between text-sm text-muted-foreground">
              <div className="flex items-center space-x-1">
                <Star className="h-3.5 w-3.5 fill-primary text-primary" />
                <span>{rating.toFixed(1)}</span>
              </div>
              <span>{year}</span>
              {episodes && <span>{episodes} eps</span>}
            </div>
          </div>
        </CardContent>
      </Card>
    </Link>
  );
};

export default DoramaCard;
