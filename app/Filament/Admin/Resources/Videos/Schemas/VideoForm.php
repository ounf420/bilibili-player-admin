<?php

namespace App\Filament\Admin\Resources\Videos\Schemas;

use App\Models\Video;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('🎬 视频信息')
                    ->schema([
                        TextInput::make('title')
                            ->label('视频标题')
                            ->required()
                            ->maxLength(500),

                        TextInput::make('url')
                            ->label('视频地址')
                            ->required()
                            ->placeholder('https://example.com/video.mp4 或 .m3u8')
                            ->helperText('支持 MP4、M3U8(HLS)、FLV 格式')
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $type = Video::detectType($state);
                                    $set('type', $type);
                                }
                            }),

                        TextInput::make('cover')
                            ->label('封面图地址')
                            ->placeholder('https://example.com/cover.jpg')
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('视频格式')
                            ->options(['mp4'=>'MP4','m3u8'=>'M3U8','flv'=>'FLV','dash'=>'DASH'])
                            ->default('mp4')->required()->disabled()->dehydrated(),

                        Textarea::make('description')
                            ->label('视频描述')->rows(3)->columnSpanFull(),
                    ]),

                Section::make('📂 影视信息')
                    ->schema([
                        Select::make('category')->label('分类')->options([
                            '电影'=>'电影','电视剧'=>'电视剧','动漫'=>'动漫','综艺'=>'综艺','纪录片'=>'纪录片',
                        ])->searchable(),
                        TextInput::make('genre')->label('类型/风格')->placeholder('如：科幻、喜剧、热血'),
                        TextInput::make('region')->label('地区')->placeholder('如：中国大陆、日本、美国'),
                        TextInput::make('year')->label('年份')->placeholder('如：2024'),
                        TextInput::make('language')->label('语言')->placeholder('如：国语、日语、英语'),
                        TextInput::make('tags')->label('标签')->placeholder('逗号分隔，如：热血,冒险'),
                    ])->columns(2),

                Section::make('🎭 演职信息')
                    ->schema([
                        TextInput::make('director')->label('导演')->placeholder('导演姓名'),
                        TextInput::make('actors')->label('演员')->placeholder('演员姓名，逗号分隔'),
                        TextInput::make('score')->label('评分')->numeric()->maxValue(10)->minValue(0)->step(0.1),
                        TextInput::make('duration')->label('时长(秒)')->numeric()->default(0),
                        TextInput::make('episode_count')->label('集数')->numeric()->default(1),
                        Toggle::make('is_ending')->label('已完结')->default(true),
                    ])->columns(3),

                Section::make('💎 VIP与展示')
                    ->schema([
                        Select::make('vip_level')->label('观看需要VIP等级')->options([
                            0=>'免费', 1=>'VIP', 2=>'SVIP',
                        ])->default(0)->required(),
                        Select::make('quality')->label('画质')->options([
                            'SD'=>'标清SD','HD'=>'高清HD','FHD'=>'超清FHD','4K'=>'4K',
                        ])->default('HD'),
                        Toggle::make('is_recommend')->label('首页推荐')->default(false),
                        TextInput::make('sort_order')->label('排序权重')->numeric()->default(0),
                        Toggle::make('enabled')->label('启用')->default(true),
                    ])->columns(3),

                Section::make('📊 播放数据')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('views')->label('播放次数')->numeric()->disabled(),
                            TextInput::make('likes')->label('点赞数')->numeric()->disabled(),
                            TextInput::make('id')->label('视频ID')->disabled(),
                        ]),
                    ]),
            ]);
    }
}
