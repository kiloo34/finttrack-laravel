import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import axios from '@/lib/axios';
import { Category } from '@/types/finance';
import { Button } from '@/components/ui/button';
import { Plus, Tags, Edit2, TrendingDown, TrendingUp } from 'lucide-react';
import CategoryModal from '@/components/categories/CategoryModal';

export default function CategoriesIndex() {
    const [categories, setCategories] = useState<Category[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedCategory, setSelectedCategory] = useState<Category | null>(null);

    const fetchCategories = async () => {
        setIsLoading(true);
        try {
            const response = await axios.get<{ data: Category[] }>('/categories');
            setCategories(response.data.data);
        } catch (error) {
            console.error('Failed to fetch categories', error);
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchCategories();
    }, []);

    const handleOpenModal = (category?: Category) => {
        setSelectedCategory(category || null);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedCategory(null);
    };

    const [expandedCategories, setExpandedCategories] = useState<Record<number, boolean>>({});

    const toggleExpand = (id: number, e: React.MouseEvent) => {
        e.stopPropagation();
        setExpandedCategories(prev => ({
            ...prev,
            [id]: !prev[id]
        }));
    };

    // Build hierarchical tree
    const rootCategories = categories.filter(c => c.parent_id === null);
    
    const incomeCategories = rootCategories.filter(c => c.type === 'income');
    const expenseCategories = rootCategories.filter(c => c.type === 'expense');

    const renderCategoryItem = (category: Category, isSubcategory = false) => {
        const children = categories.filter(c => c.parent_id === category.id);
        const Icon = category.type === 'income' ? TrendingUp : TrendingDown;
        const colorClass = category.type === 'income' ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50';
        const isExpanded = !!expandedCategories[category.id];

        return (
            <div key={category.id} className="space-y-2">
                <div 
                    className={`flex items-center justify-between rounded-xl border bg-card p-4 shadow-sm transition-colors ${isSubcategory ? 'ml-8 bg-muted/20' : ''} ${children.length > 0 ? 'cursor-pointer hover:bg-muted/50' : ''}`}
                    onClick={(e) => {
                        if (children.length > 0) toggleExpand(category.id, e);
                    }}
                >
                    <div className="flex items-center gap-3">
                        <div className={`p-2 rounded-lg ${colorClass}`}>
                            <Tags className="h-4 w-4" />
                        </div>
                        <span className="font-medium">{category.name}</span>
                        {children.length > 0 && !isSubcategory && (
                            <span className="text-xs bg-muted px-2 py-1 rounded-full text-muted-foreground flex items-center gap-1">
                                {children.length} sub
                            </span>
                        )}
                    </div>
                    <div className="flex items-center gap-2">
                        <Button 
                            variant="ghost" 
                            size="icon" 
                            onClick={(e) => {
                                e.stopPropagation();
                                handleOpenModal(category);
                            }}
                        >
                            <Edit2 className="h-4 w-4" />
                        </Button>
                        {children.length > 0 && (
                            <Button 
                                variant="ghost" 
                                size="icon" 
                                className="h-8 w-8 text-muted-foreground"
                                onClick={(e) => toggleExpand(category.id, e)}
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    className={`h-4 w-4 transition-transform duration-200 ${isExpanded ? "rotate-180" : ""}`}
                                >
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </Button>
                        )}
                    </div>
                </div>
                
                {/* Render children */}
                {children.length > 0 && isExpanded && (
                    <div className="space-y-2 mt-2 animate-in slide-in-from-top-2 fade-in duration-200">
                        {children.map(child => renderCategoryItem(child, true))}
                    </div>
                )}
            </div>
        );
    };

    return (
        <div className="flex h-full flex-1 flex-col gap-6 p-6">
            <Head title="Categories" />
            
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-semibold">Categories</h1>
                    <p className="text-sm text-muted-foreground mt-1">Organize your income and expenses.</p>
                </div>
                <Button onClick={() => handleOpenModal()}>
                    <Plus className="mr-2 h-4 w-4" /> Add Category
                </Button>
            </div>

            <div className="grid gap-6 md:grid-cols-2">
                {/* Expense Categories */}
                <div className="space-y-4">
                    <div className="flex items-center gap-2">
                        <TrendingDown className="h-5 w-5 text-red-500" />
                        <h2 className="text-xl font-medium">Expense Categories</h2>
                    </div>
                    <div className="grid gap-3">
                        {isLoading ? (
                            <p className="text-sm text-muted-foreground">Loading...</p>
                        ) : expenseCategories.length === 0 ? (
                            <div className="py-6 text-center border rounded-xl bg-card border-dashed">
                                <p className="text-sm text-muted-foreground">No expense categories yet.</p>
                            </div>
                        ) : (
                            expenseCategories.map(category => renderCategoryItem(category))
                        )}
                    </div>
                </div>

                {/* Income Categories */}
                <div className="space-y-4">
                    <div className="flex items-center gap-2">
                        <TrendingUp className="h-5 w-5 text-emerald-500" />
                        <h2 className="text-xl font-medium">Income Categories</h2>
                    </div>
                    <div className="grid gap-3">
                        {isLoading ? (
                            <p className="text-sm text-muted-foreground">Loading...</p>
                        ) : incomeCategories.length === 0 ? (
                            <div className="py-6 text-center border rounded-xl bg-card border-dashed">
                                <p className="text-sm text-muted-foreground">No income categories yet.</p>
                            </div>
                        ) : (
                            incomeCategories.map(category => renderCategoryItem(category))
                        )}
                    </div>
                </div>
            </div>

            <CategoryModal 
                isOpen={isModalOpen} 
                onClose={handleCloseModal} 
                onSuccess={fetchCategories}
                category={selectedCategory}
                allCategories={categories}
            />
        </div>
    );
}
