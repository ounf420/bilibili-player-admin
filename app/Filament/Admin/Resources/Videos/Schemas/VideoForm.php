<?php

namespace App\Filament\Admin\Resources\Videos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('🎬 基本信息')
                    ->schema([
                        TextInput::make('title')
                            ->label('视频标题')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Textarea::make('url')
                            ->label('视频地址')
                            ->placeholder('https://example.com/video.mp4 或 .m3u8')
                            ->rows(2)
                            ->required()
                            ->helperText('支持 MP4、M3U8 格式')
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('cover')
                                    ->label('封面图')
                                    ->placeholder('https://example.com/cover.jpg'),

                                Select::make('type')
                                    ->label('视频类型')
                                    ->options([
                                        'mp4' => 'MP4',
                                        'm3u8' => 'M3U8',
                                        'hls' => 'HLS',
                                    ])
                                    ->default('mp4'),

                                TextInput::make('duration')
                                    ->label('时长(秒)')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Textarea::make('description')
                            ->label('视频简介')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('📂 分类信息')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('category')
                                    ->label('分类')
                                    ->options([
                                        '电影' => '电影',
                                        '电视剧' => '电视剧',
                                        '动漫' => '动漫',
                                        '综艺' => '综艺',
                                        '纪录片' => '纪录片',
                                        '短片' => '短片',
                                        'MV' => 'MV',
                                        '其他' => '其他',
                                    ])
                                    ->searchable(),

                                Select::make('genre')
                                    ->label('题材')
                                    ->options([
                                        '动作' => '动作',
                                        '喜剧' => '喜剧',
                                        '爱情' => '爱情',
                                        '科幻' => '科幻',
                                        '恐怖' => '恐怖',
                                        '悬疑' => '悬疑',
                                        '剧情' => '剧情',
                                        '战争' => '战争',
                                        '奇幻' => '奇幻',
                                        '历史' => '历史',
                                        '家庭' => '家庭',
                                        '犯罪' => '犯罪',
                                    ])
                                    ->searchable(),

                                Select::make('region')
                                    ->label('地区')
                                    ->options([
                                        '中国大陆' => '中国大陆',
                                        '中国香港' => '中国香港',
                                        '中国台湾' => '中国台湾',
                                        '日本' => '日本',
                                        '韩国' => '韩国',
                                        '美国' => '美国',
                                        '英国' => '英国',
                                        '其他' => '其他',
                                    ])
                                    ->searchable(),

                                TextInput::make('year')
                                    ->label('年份')
                                    ->placeholder('2026'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('language')
                                    ->label('语言')
                                    ->placeholder('国语'),

                                TextInput::make('tags')
                                    ->label('标签')
                                    ->placeholder('动作,冒险,科幻')
                                    ->helperText('多个标签用逗号分隔'),

                                Select::make('quality')
                                    ->label('画质')
                                    ->options([
                                        'SD' => '标清',
                                        'HD' => '高清',
                                        'FHD' => '全高清',
                                        '4K' => '4K',
                                    ])
                                    ->default('HD'),
                            ]),
                    ]),

                Section::make('👥 演职信息')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('director')
                                    ->label('导演')
                                    ->placeholder('张艺谋'),

                                TextInput::make('actors')
                                    ->label('演员')
                                    ->placeholder('演员1,演员2,演员3')
                                    ->helperText('多个演员用逗号分隔'),
                            ]),

                        Grid::make(4)
                            ->schema([
                                TextInput::make('score')
                                    ->label('评分')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(10)
                                    ->step(0.1),

                                TextInput::make('episode_count')
                                    ->label('集数')
                                    ->numeric()
                                    ->default(1),

                                Toggle::make('is_ending')
                                    ->label('已完结')
                                    ->default(true),

                                Toggle::make('is_recommend')
                                    ->label('推荐')
                                    ->default(false)
                                    ->helperText('推荐视频会在首页展示'),
                            ]),
                    ]),

                Section::make('⚙️ 显示设置')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('enabled')
                                    ->label('启用')
                                    ->default(true),

                                TextInput::make('sort_order')
                                    ->label('排序')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('数字越大越靠前'),

                                TextInput::make('views')
                                    ->label('播放量')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }
}
